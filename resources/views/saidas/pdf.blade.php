<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .tabela{
            text-align: center;
            vertical-align: middle;
            padding: 10px;
            margin-bottom: 10px;
        }
        .centro{
            text-align: center;
            vertical-align: middle;
            padding-left: 10px;
            padding-right: 10px;
        }
        .esquerda{
            text-align: left;
            vertical-align: middle;
            padding-left: 10px;
        }
        .direita{
            text-align: right;
            vertical-align: middle;
            padding-right: 10px;
        }
        .borderThin{
            border: 1px solid black;
        }
        table.borderThinb th{
            border-bottom: 1px solid rgb(73, 73, 73);
            border-right: 1px solid rgb(73, 73, 73);
        }
        table.borderThinb td{
            border-bottom: 1px dotted rgb(73, 73, 73);
            border-right: 1px solid rgb(73, 73, 73);
        }
        /*<div class="page-break"></div>*/
        .page-break {
            page-break-after: always;
        }
        [type="checkbox"]
        {
            vertical-align: bottom;
        }
    </style>
    <title>Ficha de ordem de serviço</title>
</head>
<body>

    <table style="width:100%; border: 0px solid black;">
        <tr>
            <td class="centro">
                <img src="img/mpengenharia.png" width="200px;" style="margin-right: 15px; ">
            </td>
            <td class="centro" >
                <h2>ORDEM DE SERVIÇO</h2>
            </td>
            <td class="centro" >
                <img src="img/logosvma.png" width="150px" style="margin-left: 15px;">
            </td>
        </tr>
    </table>
    <hr>

    <table style="width:100%; border: 1px solid black; border-collapse: collapse;">
        <tr>
            <td colspan="2" class="centro borderThin">
                <p>Contrato n° <b>045/SVMA/2022</b>. Processo Adiministrativo: <b>6027.2018/0005691-9</b><br>
                    Contratada: <b>MPE Engenharia e Serviços S.A</b>  Data: <b>{{ $ordem->created_at_formatado }}</b></p>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="esquerda borderThin">
                    <p><b>Unidade Estratégica de manutenção:</b> {{ $ordem->local_servico->nome }}</p>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="esquerda borderThin">
                    <p><b>Local de Serviço:</b> {{ $ordem->local_servico->nome }}</p>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="tabela borderThin">
                <table style="width: 100%">
                    <tr>
                        <td style="text-align: left;">
                            Solicitamos a execução dos serviços abaixo:
                        </td>
                        <td style="text-align: right;">
                            <b>OS N°</b> {{ $ordem->id }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: left;">
                            {{ $ordem->especificacao }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="tabela borderThin">
                <table style="width: 100%">
                    <tr>
                        <td>Tipo de manutenção</td>
                        <td>
                            <input type="checkbox" class="form-ckeck-input" name="corretiva" id="corretiva">
                            <label for="corretiva" style="word-wrap:break-word">Corretiva</label>
                        </td>
                        <td>
                            <input type="checkbox" class="form-ckeck-input" name="preventiva" id="preventiva">
                            <label for="preventiva" style="word-wrap:break-word">Preventiva</label>
                        </td>
                        <td>
                            <input type="checkbox" class="form-ckeck-input" name="preditiva" id="preditiva">
                            <label for="preditiva" style="word-wrap:break-word">Preditiva</label>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="tabela borderThin">
                <table style="width: 100%">
                    <tr>
                        <td style="text-align: center;"><br>
                            _______________________________<br>
                            Fiscal do contrato SVMA<br>RF:______________
                        </td>
                        <td style="text-align: center;"><br>
                            _______________________________<br>
                            Responsável técnico MPE<br>CPF:______________
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="tabela borderThin">
                <p><strong>Período de execução dos serviços de:</strong> ___/___/____ a  ___/___/____</p>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="tabela borderThin">
                <strong>Equipe</strong>
                <hr>
                <table class="borderThinb" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <th width="40%" style="text-align: left;">Nome</th>
                        <th width="20%" style="text-align: left;">Data</th>
                        <th width="10%" style="text-align: left;">Tempo</th>
                    </tr>
                    @foreach ($profissionais as $dado_profissional)
                    <tr>
                        <td>{{ $dado_profissional->nome }}</td>
                        <td>{{ $dado_profissional->data_inicio_formatada }}</td>
                        <td>{{ $dado_profissional->horas_empregadas }}h</td>
                    </tr>
                    @endforeach
            </td>
        </tr>
    </table>
    <div class="page-break"></div>
    <table style="width:100%; border: 1px solid black; border-collapse: collapse;">
        <tr>
            <td colspan="2" class="tabela borderThin">
                <strong>Materiais utilizados para o serviço:</strong>
                <hr>
                <table class="borderThinb" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <th width="10%" style="text-align: left;">Tipo</th>
                        <th width="50%" style="text-align: left;">Descrição</th>
                        <th width="10%" style="text-align: left;">Solicitado</th>
                        <th width="10%" style="text-align: left;">Enviado</th>
                        <th width="10%" style="text-align: left;">Usado</th>
                        <th width="10%" style="text-align: left;">Retorno</th>
                    </tr>
                    @foreach ($ordem_servico_items as $ordem_servico_item)
                    <tr>
                        <td>{{ $ordem_servico_item->item->tipo_item->nome }}</td>
                        <td>{{ $ordem_servico_item->item->nome }}</td>
                        <td>{{ $ordem_servico_item->quantidade }}</td>
                        <td>@if(in_array($ordem_servico_item->item_id,$saida_items))
                            {{ $saida_items[$ordem_servico_item->item_id]->enviado }}
                        @endif</td>
                        <td>@if(in_array($ordem_servico_item->item_id,$saida_items)) {{ $saida_items[$ordem_servico_item->item_id]->usado }} @endif</td>
                        <td>@if(in_array($ordem_servico_item->item_id,$saida_items)) {{ $saida_items[$ordem_servico_item->item_id]->retorno }} @endif</td>
                    </tr>
                    @endforeach
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="tabela borderThin">
                <table style="width: 100%">
                    <tr>
                        <td>
                            <input type="checkbox" class="form-ckeck-input" name="manutencao"  id="manutencao">
                            <label class="form-check-label" for="manutencao">Atesto que os serviços foram realizados a contento</label>
                        </td>
                    </tr><tr>
                        <td>
                            <input type="checkbox" class="form-ckeck-input" name="manutencao"  id="manutencao">
                            <label class="form-check-label" for="manutencao">Não atesto que os serviços foram realizados a contento</label>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="centro borderThin">
                <p>Data de devolução da OS: ___/___/____</p>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="centro borderThin">
                <table style="width: 100%">
                    <tr>
                        <td><br>________________________<br> Encarregado MPE</td>
                        <td><br>________________________<br> Administrador</td>
                        <td><br>________________________<br> Fiscal SVMA</td>
                    </tr>
                    <tr>
                        <td>CPF:</td>
                        <td>RF:</td>
                        <td>CPF:</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
