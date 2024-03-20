#import mysql.connector as mariadb
import math
import pandas as pd
from collections import defaultdict
from datetime import datetime


def id_medida(valor):
    if valor == 'JG':
        return 9
    elif valor == 'Pacote':
        return 10
    elif valor == 'MT':
        return 3
    elif valor == 'PÇ':
        return 4
    elif valor == 'Uni':
        return 8
    elif valor == 'Unidade':
        return 8
    return 0
        

#leitura da planilha
dataframes = {}
sheets = []

#with pd.ExcelFile(r'Cadastro de Autos - OFICIAL +GEO.xlsx') as xls:
with pd.ExcelFile(r'items_marcenaria.xlsx') as xls:
    for sheet in xls.sheet_names:
        sheets.append(sheet)
        dataframes[sheet] = xls.parse(sheet)

payload = defaultdict(dict)

for sheet, dataframe in dataframes.items():
    for info in dataframe.values:
        item = int(info[0])

        for tupled_df, info_ in zip(dataframe.items(), info):
            column = tupled_df[0].strip()
            try:
                info_ = info_.strip() if pd.notnull(info_) else ''
            except:
                pass
            payload[item][column] = info_
    break
print("Leitura da planilha concluída.")

# Abrindo arquivo SQL para inserir as queries
f = open('insertMarcenaria.sql', 'w')

# db = mariadb.connect(user='root', host="localhost", password='', database='svma_listas_sharepoint', charset='utf8')
# cursor = db.cursor()

#depois de inserir os itens de elétrica, devemos chegar em 111 na chave primária
id_item = 112
for item, info in payload.items():
    texto_item = info["Descrição"].splitlines()
    nome = texto_item[0]
    descricao = texto_item[-1] if len(texto_item) > 1 else ''
    descricao = descricao.replace('Especificação Técnica: ','').replace('Especificação Técnica:','')
    
    medida_id = id_medida(info["Unidade"])
    qtd = int(info["Quantidade"]) if len(str(info["Quantidade"])) > 1 else 0
    
    # print(nome)
    # print(descricao)
    # print('=============\n')
    
     # escrevendo linha com query: tipo_item_id 2 = marcenaria
    f.write((f"INSERT INTO items (departamento_id,tipo_item_id,medida_id,nome,descricao,created_at,updated_at)"+
             f"VALUES (3,2,{medida_id},'{nome}','{descricao}',NOW(),NOW());\n"))
    
     # escrevendo linha com query: local_id 2 = UEM Leopoldina
    if (qtd > 0):
        f.write((f"INSERT INTO inventarios (departamento_id,item_id,local_id,quantidade,created_at,updated_at)"+
                 f"VALUES (3,{id_item},2,{qtd},NOW(),NOW());\n"))
    
    # considerando que a tabela de itens estã em autoincrement igual a 1 para esta lista de itens
    id_item += 1


print("Arquivo SQL gerado com sucesso!")
f.close()
print("Concluído.")