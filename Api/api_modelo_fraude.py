from flask import Flask, request, jsonify
import joblib
import pandas as pd

app = Flask(__name__)

# Cargar el modelo entrenado
modelo = joblib.load('modelo_fraude_financiero.joblib')

@app.route('/prediccion', methods=['POST'])
def prediccion():
    datos = request.get_json(force=True)
    df = pd.DataFrame(datos)

    #Ejecutar el modelo
    predicciones = modelo.predict(df)

    # Obtener el valor de la predicción (suponiendo que solo hay un registro)
    prediccion = predicciones[0]

    # Crear un mensaje basado en la predicción
    if prediccion >= 1:
        resultado = 'La transacción es fraudulenta'
    else:
        resultado = 'La transacción no es fraudulenta'

    return jsonify(resultado)

if __name__ == '__main__':
    app.run(port=5000, debug=False)
