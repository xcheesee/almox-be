#import mysql.connector as mariadb
import math
import pandas as pd
from collections import defaultdict
from datetime import datetime


def id_medida(valor):
    if valor == 'CX':
        return 1
    elif valor == 'M²':
        return 2
    elif valor == 'MT':
        return 3
    elif valor == 'SC':
        return 4
    elif valor == 'Unidade':
        return 5
    elif valor == 'PÇ':
        return 6
    elif valor == 'GL':
        return 7
    elif valor == 'LT':
        return 8
    elif valor == 'PT':
        return 9
    elif valor == 'SC (8Kg)':
        return 10
    elif valor == 'Unidade (250g)':
        return 11
    elif valor == 'Unidade (280g)':
        return 12
    return 0
        

#leitura da planilha
dataframes = {}
sheets = []

#with pd.ExcelFile(r'Cadastro de Autos - OFICIAL +GEO.xlsx') as xls:
with pd.ExcelFile(r'items_carpintaria.xlsx') as xls:
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
f = open('insertCarpintaria.sql', 'w')

# db = mariadb.connect(user='root', host="localhost", password='', database='svma_listas_sharepoint', charset='utf8')
# cursor = db.cursor()

for item, info in payload.items():
    texto_item = info["Descrição"].splitlines()
    nome = texto_item[0]
    descricao = texto_item[-1] if len(texto_item) > 1 else ''
    descricao = descricao.replace('Especificação Técnica: ','').replace('Especificação Técnica:','')
    
    medida_id = id_medida(info["Unidade"])
    
    # print(nome)
    # print(descricao)
    # print('=============\n')
    
     # escrevendo linha com query {info[]} m²
    f.write((f"INSERT INTO items (departamento_id,tipo_item_id,medida_id,nome,descricao,created_at,updated_at)"+
             f"VALUES (3,2,{medida_id},'{nome}','{descricao}',NOW(),NOW());\n"))


print("Arquivo SQL gerado com sucesso!")
f.close()
print("Concluído.")