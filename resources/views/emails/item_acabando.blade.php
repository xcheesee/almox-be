<h1>Items a repor no estoque</h1>
<div>
    <p>Favor verificar as quantidades dos itens abaixo e solicitar o quanto antes a reposição no estoque:</p>
    <p>
        <table border="1">
            <tr>
                <th>Item</th>
                <th>Quantidade</th>
                <th>Base</th>
                <th>Alertar quando menor ou igual a</th>
            </tr>
            @foreach ($inventarios as $inventario)
                <tr>
                    <td>{{ $inventario->item->nome }}</th>
                    <td>{{ $inventario->quantidade }}</th>
                    <td>{{ $inventario->local->nome }}</th>
                    <td>{{ $inventario->qtd_alerta }}</th>
                </tr>
            @endforeach
        </table>
    </p>
</div>
