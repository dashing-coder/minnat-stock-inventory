#!/usr/bin/env python3
"""Read-only diagnostic for a trusted phpMyAdmin MySQL ERP export. No database writes.
Usage: python offline_sql_audit.py /path/to/export.sql /path/to/laravel/project
Outputs JSON on stdout; does NOT print customer names, passwords, or addresses.
This is an export snapshot diagnostic, not a replacement for MySQL integration QA.
"""
import sys,re,json,collections,os
from pathlib import Path
from decimal import Decimal

if len(sys.argv) != 3:
    raise SystemExit('Usage: python offline_sql_audit.py /path/to/export.sql /path/to/laravel/project')
sql_path=Path(sys.argv[1]); app=Path(sys.argv[2]); text=sql_path.read_text(encoding='utf-8-sig')
insert_re=re.compile(r'INSERT INTO `([^`]+)` \(([^\n]+)\) VALUES\s*(.*?);\s*(?=\n|\Z)',re.S)

def parse_values(s):
    rows=[]; row=[]; field=[]; quoted=False; in_tuple=False; idx=0; n=len(s)
    while idx<n:
        ch=s[idx]
        if quoted:
            if ch=='\\' and idx+1<n:
                nxt=s[idx+1]; field.append({'0':'\0','n':'\n','r':'\r','t':'\t','Z':'\x1a'}.get(nxt,nxt)); idx+=2; continue
            if ch=="'":
                if idx+1<n and s[idx+1]=="'": field.append("'"); idx+=2; continue
                quoted=False; idx+=1; continue
            field.append(ch); idx+=1; continue
        if ch=="'": quoted=True; idx+=1; continue
        if ch=='(':
            if not in_tuple: row=[]; field=[]; in_tuple=True
        elif ch==',' and in_tuple:
            token=''.join(field).strip(); row.append(None if token.upper()=='NULL' else token); field=[]
        elif ch==')' and in_tuple:
            token=''.join(field).strip(); row.append(None if token.upper()=='NULL' else token)
            rows.append(row); row=[]; field=[]; in_tuple=False
        elif in_tuple: field.append(ch)
        idx+=1
    if quoted or in_tuple: raise ValueError('Unclosed MySQL string/tuple')
    return rows

data=collections.defaultdict(list)
for m in insert_re.finditer(text):
    name,cols,vals=m.groups(); cols=re.findall(r'`([^`]+)`',cols)
    for row in parse_values(vals):
        if len(cols)!=len(row): raise ValueError(f'{name}: wrong row length {len(row)} vs {len(cols)}')
        data[name].append(dict(zip(cols,row)))

def num(v): return Decimal(str(v or 0))
def money(v): return str(num(v).quantize(Decimal('0.01')))
def pid(x): return str(x) if x is not None else ''

files={p.stem for p in (app/'database'/'migrations').glob('*.php')}; records={r['migration'] for r in data['migrations']};
posted={j['id']:j for j in data['journal_entries'] if j['status']=='posted'}
lines=[l for l in data['journal_lines'] if l['journal_entry_id'] in posted]
debits=sum((num(l['debit']) for l in lines),Decimal(0)); credits=sum((num(l['credit']) for l in lines),Decimal(0))
by_journal=collections.defaultdict(lambda:[Decimal(0),Decimal(0),0])
for l in lines:
 x=by_journal[l['journal_entry_id']]; x[0]+=num(l['debit']);x[1]+=num(l['credit']);x[2]+=1
unbalanced=[{'journal_id':j,'difference':money(x[0]-x[1]),'line_count':x[2]} for j,x in by_journal.items() if abs(x[0]-x[1])>=Decimal('.01') or x[2]<2]
accts={a['id']:a for a in data['accounts']}
amounts=collections.defaultdict(Decimal)
for l in lines: amounts[accts.get(l['account_id'],{}).get('code','unknown')]+=num(l['debit'])-num(l['credit'])

sale={x['id']:x for x in data['sales']}; purchase={x['id']:x for x in data['purchases']};
sale_items=collections.defaultdict(set)
for x in data['sale_items']: sale_items[x['sale_id']].add(x['product_id'])
purchase_items=collections.defaultdict(set)
for x in data['purchase_items']: purchase_items[x['purchase_id']].add(x['product_id'])
sri=collections.defaultdict(list)
for x in data['sale_return_items']: sri[x['sale_return_id']].append(x)
pri=collections.defaultdict(list)
for x in data['purchase_return_items']: pri[x['purchase_return_id']].append(x)
return_issues=[]
for tbl,lookup,children,original_children,party,foreign in [('sale_returns',sale,sri,sale_items,'customer_id','sale_id'),('purchase_returns',purchase,pri,purchase_items,'supplier_id','purchase_id')]:
 for row in data[tbl]:
  if row['status']!='posted': continue
  origin=lookup.get(row[foreign]); flags=[]
  if not origin: flags.append('missing original')
  else:
   if origin['status']!='posted':flags.append('original not posted')
   if origin[party]!=row[party]:flags.append('party differs')
   if origin['warehouse_id']!=row['warehouse_id']:flags.append('warehouse differs')
   bad=[x['product_id'] for x in children[row['id']] if x['product_id'] not in original_children[origin['id']]]
   if bad:flags.append('product(s) absent from original: '+','.join(sorted(set(bad))))
  if flags:return_issues.append({'table':tbl,'id':row['id'],'linked_document_id':row[foreign],'reasons':flags})

qty=collections.defaultdict(Decimal)
for x in data['stock_movements']:
 qty[(x['warehouse_id'],x['product_id'])]+=num(x['quantity_in'])-num(x['quantity_out'])
layer=collections.defaultdict(Decimal)
for x in data['inventory_cost_layers']:layer[(x['warehouse_id'],x['product_id'])]+=num(x['quantity_remaining'])
stock={(x['warehouse_id'],x['product_id']):num(x['quantity']) for x in data['warehouse_stocks']}
keys=set(stock)|set(qty)|set(layer)
movement_mismatch=[{'warehouse_id':x[0],'product_id':x[1],'stock':str(stock.get(x,0)),'movements':str(qty.get(x,0))} for x in keys if abs(stock.get(x,0)-qty.get(x,0))>Decimal('0.00011')]
layer_mismatch=[{'warehouse_id':x[0],'product_id':x[1],'stock':str(stock.get(x,0)),'layers':str(layer.get(x,0))} for x in keys if abs(stock.get(x,0)-layer.get(x,0))>Decimal('0.00011')]
by_transfer=collections.defaultdict(list)
for r in data['stock_movements']:
 if r['source_type']=='stock_transfer': by_transfer[r['source_id']].append(r)
transfer_items=collections.defaultdict(list)
for r in data['stock_transfer_items']: transfer_items[r['stock_transfer_id']].append(r)
transfer_issues=[]
for r in data['stock_transfers']:
 if r['status']!='posted':continue
 rows=by_transfer[r['id']];line=transfer_items[r['id']]
 for item in line:
  pr=item['product_id']; amt=num(item['quantity']);out=sum((num(a['quantity_out']) for a in rows if a['warehouse_id']==r['from_warehouse_id'] and a['product_id']==pr),Decimal(0));inc=sum((num(a['quantity_in']) for a in rows if a['warehouse_id']==r['to_warehouse_id'] and a['product_id']==pr),Decimal(0));
  if abs(amt-out)>Decimal('0.00011') or abs(amt-inc)>Decimal('0.00011'):transfer_issues.append({'transfer_id':r['id'],'product_id':pr,'expected':str(amt),'out':str(out),'in':str(inc)})

# Mirror the read-only InventoryAuditService valuation formula for the exported snapshot.
product_by_id={r['id']:r for r in data['products']}
warehouse_valuation=Decimal(0)
for r in data['warehouse_stocks']:
    p=product_by_id.get(r['product_id'],{})
    key=(r['warehouse_id'],r['product_id']); this_qty=num(r['quantity']); layer_qty=layer.get(key,Decimal(0))
    avg=num(r.get('average_cost')); fallback=avg if avg>0 else num(p.get('purchase_price'))
    layer_val=sum((num(x['quantity_remaining'])*num(x['unit_cost']) for x in data['inventory_cost_layers'] if (x['warehouse_id'],x['product_id'])==key),Decimal(0))
    fifo_val=layer_val+max(Decimal(0),this_qty-layer_qty)*fallback
    warehouse_valuation+=fifo_val if p.get('costing_method')=='fifo' else this_qty*fallback
# Compare exported role grants with fixed role ceilings in checked-out PHP source.
role_source=(app/'app/Support/RoleSecurity.php').read_text(encoding='utf-8')
role_map={r['id']:r['name'] for r in data['roles']}
perm_map={r['id']:r['name'] for r in data['permissions']}
role_excess=[]
for const,role_name in [('SELLER_ALLOWED','Seller'),('SALES_EXEC_ALLOWED','Sales Executive')]:
    match=re.search(r'public const '+const+r'\s*=\s*\[(.*?)\];',role_source,re.S)
    allowed=set(re.findall(r"'([^']+)'",match.group(1))) if match else set()
    for assignment in data['role_has_permissions']:
        if role_map.get(assignment['role_id'])==role_name:
            name=perm_map.get(assignment['permission_id'])
            if name and name not in allowed:role_excess.append({'role':role_name,'permission':name})
# No history or personal data is emitted in report.
report={
 'scope':'Read-only 2026-10-27 SQL export plus Laravel source; not a live database test',
 'tables_with_insert_rows':len(data),'schema_create_table_count':len(re.findall(r'^CREATE TABLE `',text,re.M)),
 'migration_files':len(files),'migration_records':len(records),'pending_in_this_export':sorted(files-records),
 'posted_journal_lines':len(lines),'posted_total_debit':money(debits),'posted_total_credit':money(credits),'posted_difference':money(debits-credits),'posted_unbalanced_journal_entries':unbalanced,
 'bank_account_gl_debit_minus_credit':money(amounts.get('1010',0)),'inventory_account_gl_debit_minus_credit':money(amounts.get('1200',0)),
 'stock_valuation_estimate':money(warehouse_valuation),'inventory_gl_minus_stock_difference':money(amounts.get('1200',0)-warehouse_valuation),
 'inventory_stock_rows':len(data['warehouse_stocks']),'stock_movement_rows':len(data['stock_movements']),'cost_layer_rows':len(data['inventory_cost_layers']),
 'stock_vs_movement_mismatch_count':len(movement_mismatch),'stock_vs_cost_layer_mismatch_count':len(layer_mismatch),
 'stock_movement_mismatch_examples':movement_mismatch[:5],'stock_cost_layer_mismatch_examples':layer_mismatch[:5],
 'posted_warehouse_transfer_issues':transfer_issues, 'posted_return_reference_issues':return_issues,
 'recorded_bank_reconciliations':len(data['bank_reconciliations']),'recorded_bank_cleared_items':len(data['bank_reconciliation_items']),
 'legacy_role_excess_permissions':role_excess,
 'source_has_env_example':(app/'.env.example').exists(),
 'missing_runtime_dirs':[p for p in ['storage/framework/sessions','storage/framework/views','storage/framework/cache/data','storage/logs','bootstrap/cache'] if not (app/p).is_dir()]
}
print(json.dumps(report,indent=2,ensure_ascii=False,default=str))
