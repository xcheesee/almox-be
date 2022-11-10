<html>
<head>
    <style>
        .centro{
            text-align: center;
            vertical-align: middle;
        }

        .footer {
            position: fixed;
            padding: 10px 10px 0px 10px;
            bottom: 0;
            width: 100%;
            /* Height of the footer*/
            height: 10px;
        }
        .borderThin{
            border: 1px dotted gray;
        }
    </style>
</head>
<body>
    <table style="width:100%; border: 1px solid black;">
        <tr>
            <td class="centro" rowspan="2">
                <img src="img/brasaopmsp.png" height="auto" width="100" alt="">
            </td>
            <td class="centro" > Prefeitura do Município de São Paulo</td>
        </tr>
        <tr>
            <td class="centro" > Baixa da Ordem de Serviço Nº {{ $ordem->id }}</td>
        </tr>
    </table>
    <br>
    <table style="width:100%; border: 1px solid black;">
        <tr>
            <td> Origem</td><td> Local de Serviço </td><td> Profissional </td>
        </tr>
        <tr>
            <td>{{ $ordem->origem->nome }}</td><td>{{ $ordem->local_servico->nome }}</td><td>{{ $ordem->profissional }}</td>
        </tr>
    </table>
    <br>
    <table style="width:100%; border: 1px solid black;">
        <tr>
            <td> Especificação do Serviço: </td>
        </tr>
        <tr>
            <td>{{ $ordem->especificacao }}</td>
        </tr>
    </table>
    <br>
    <table style="width:100%; border: 1px solid black;">
        <tr>
            <td> Horas de execução</td><td> Data de início do serviço </td><td> Data de fim do serviço </td>
        </tr>
        <tr>
            <td>{{ $ordem->horas_execucao }} hora(s)</td><td>{{ $ordem->data_inicio_formatada }}</td><td>{{ $ordem->data_fim_formatada }}</td>
        </tr>
    </table>
    <br>
    <table style="width:100%; border: 1px solid black; border-collapse: collapse;">
        <thead style="width:100%; border-bottom: 1px solid black;">
            <tr>
                <th align="left" style="width:70%">Material Usado</th>
                <th align="left" style="width:10%">Enviado</th>
                <th align="left" style="width:10%">Usado</th>
                <th align="left" style="width:10%">Retorno</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dados as $saida_item)
            <tr>
                <td class="borderThin">{{ $saida_item->item->nome }}</td>
                <td class="borderThin">{{ $saida_item->enviado }}</td>
                <td class="borderThin">{{ $saida_item->usado }}</td>
                <td class="borderThin">{{ $saida_item->retorno }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <table style="width:100%; border: 1px solid black;">
        <tr>
            <td>Baixa efetuada em</td>
            <td>{{ $saida->baixa_datahora_formatada }}</td>
        </tr>
        <tr>
            <td>Responsável</td>
            <td>{{ $saida->baixa_user->name }}</td>
        </tr>
    </table>
    </body>
</html>
