<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detección de Fraude Financiero</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 50px;
        }
        .container {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Detección de Fraude Financiero</h5>
                <form id="fraudeForm">
                    <div class="form-group">
                        <label for="monto">Monto</label>
                        <input type="number" class="form-control" id="monto" name="monto" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="saldoAntiguoOrg">Saldo Antiguo Origen</label>
                        <input type="number" class="form-control" id="saldoAntiguoOrg" name="saldoAntiguoOrg" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="nuevoSaldoOrg">Nuevo Saldo Origen</label>
                        <input type="number" class="form-control" id="nuevoSaldoOrg" name="nuevoSaldoOrg" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="saldoAntiguoDest">Saldo Antiguo Destino</label>
                        <input type="number" class="form-control" id="saldoAntiguoDest" name="saldoAntiguoDest" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="nuevoSaldoDest">Nuevo Saldo Destino</label>
                        <input type="number" class="form-control" id="nuevoSaldoDest" name="nuevoSaldoDest" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="tipo">Tipo de Transacción</label>
                        <select class="form-control" id="tipo" name="tipo" required>
                            <option value="CASH_OUT">CASH OUT</option>
                            <option value="DEBIT">DEBIT</option>
                            <option value="PAYMENT">PAYMENT</option>
                            <option value="TRANSFER">TRANSFER</option>
                        </select>
                    </div>
                    <input type="hidden" id="tipo_CASH_OUT" name="tipo_CASH_OUT" value="0">
                    <input type="hidden" id="tipo_DEBIT" name="tipo_DEBIT" value="0">
                    <input type="hidden" id="tipo_PAYMENT" name="tipo_PAYMENT" value="0">
                    <input type="hidden" id="tipo_TRANSFER" name="tipo_TRANSFER" value="0">
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </form>
                <div id="resultado" class="mt-4"></div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function(){
            $('#tipo').change(function() {
                var tipoSeleccionado = $(this).val();
                $('#tipo_CASH_OUT').val(tipoSeleccionado == 'CASH_OUT' ? 1 : 0);
                $('#tipo_DEBIT').val(tipoSeleccionado == 'DEBIT' ? 1 : 0);
                $('#tipo_PAYMENT').val(tipoSeleccionado == 'PAYMENT' ? 1 : 0);
                $('#tipo_TRANSFER').val(tipoSeleccionado == 'TRANSFER' ? 1 : 0);
            });

            $('#fraudeForm').on('submit', function(event){
                event.preventDefault();
                
                var formData = {
                    monto: parseFloat($('#monto').val()),
                    saldoAntiguoOrg: parseFloat($('#saldoAntiguoOrg').val()),
                    nuevoSaldoOrg: parseFloat($('#nuevoSaldoOrg').val()),
                    saldoAntiguoDest: parseFloat($('#saldoAntiguoDest').val()),
                    nuevoSaldoDest: parseFloat($('#nuevoSaldoDest').val()),
                    tipo_CASH_OUT: parseInt($('#tipo_CASH_OUT').val()),
                    tipo_DEBIT: parseInt($('#tipo_DEBIT').val()),
                    tipo_PAYMENT: parseInt($('#tipo_PAYMENT').val()),
                    tipo_TRANSFER: parseInt($('#tipo_TRANSFER').val())
                };
                
                $.ajax({
                    url: 'procesar.php',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(formData),
                    success: function(response){
                        if (response) {
							// Deserializar el JSON
							var jsonObject = JSON.parse(response);
							if (jsonObject.resultado == 'La transacción no es fraudulenta') {
								$('#resultado').html('<div class="alert alert-success">' + jsonObject.resultado + '</div>');
							}
                            else {
								$('#resultado').html('<div class="alert alert-warning">' + jsonObject.resultado + '</div>');
							}
                        } else {
                            $('#resultado').html('<div class="alert alert-danger">Error en la respuesta: ' + JSON.stringify(response) + '</div>');
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#resultado').html('<div class="alert alert-danger">Error: ' + error + '</div>');
                    }
                });
            });
        });
    </script>
</body>
</html>
