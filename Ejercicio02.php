<?php
abstract class Estadistica {
    abstract public function calcularMedia(array $datos): float; 
    abstract public function calcularMediana(array $datos): float; 
    abstract public function calcularModa(array $datos): array;
}

class EstadisticaBasica extends Estadistica {
    public function calcularMedia(array $datos): float { 
        if (empty($datos)) {
            return 0.0;
        }
        return array_sum($datos) / count($datos);
    }

    public function calcularMediana(array $datos): float {
        if (empty($datos)) {
            return 0.0;
        }
        sort($datos); 
        $n = count($datos);
        $mitad = floor($n / 2);

        if ($n % 2 === 1) { 
            return $datos[$mitad];
        } else { 
            return ($datos[$mitad - 1] + $datos[$mitad]) / 2;
        }
    }

    public function calcularModa(array $datos): array {
        if (empty($datos)) {
            return [];
        }

        $frecuencias = [];
        foreach ($datos as $valor) {
            $frecuencias[(string)$valor] = ($frecuencias[(string)$valor] ?? 0) + 1;
        }

        $maxFrecuencia = 0;
        foreach ($frecuencias as $valor_str => $frecuencia) {
            if ($frecuencia > $maxFrecuencia) {
                $maxFrecuencia = $frecuencia;
            }
        }

        $modas = [];
        foreach ($frecuencias as $valor_str => $frecuencia) {
            if ($frecuencia === $maxFrecuencia) {
                $modas[] = (float) $valor_str; 
            }
        }
        if ($maxFrecuencia === 1 && count($modas) === count($datos) && count($datos) > 0) {
             return [];
        }
        return $modas;
    }
}

function generarInforme(array $conjuntos_de_datos): array {
    $informe = [];
    $estadisticaBasica = new EstadisticaBasica();

    foreach ($conjuntos_de_datos as $identificador => $datos) {
        $media = $estadisticaBasica->calcularMedia($datos);
        $mediana = $estadisticaBasica->calcularMediana($datos);
        $moda = $estadisticaBasica->calcularModa($datos);

        $informe[$identificador] = [
            'media' => $media,
            'mediana' => $mediana,
            'moda' => $moda
        ];
    }
    return $informe;
}

$todos_los_datos = []; 

echo "Bienvenido al generador de informes estadísticos.\n";
echo "Puede ingresar varios conjuntos de datos. Ingrese 'fin' como identificador para terminar.\n\n";

while (true) {
    echo "Ingrese el identificador para el conjunto de datos (o 'fin' para terminar): ";
    $identificador = trim(fgets(STDIN));

    if (strtolower($identificador) === 'fin') {
        break;
    }

    echo "Ingrese los números para el conjunto '" . $identificador . "', separados por comas (ej. 10,20,30,40): ";
    $entrada_numeros = trim(fgets(STDIN));

    $numeros_str = explode(',', $entrada_numeros);
    $numeros = [];
    foreach ($numeros_str as $num) {
        $num_limpio = trim($num);
        if (is_numeric($num_limpio)) {
            $numeros[] = (float) $num_limpio;
        }
    }

    if (!empty($numeros)) {
        $todos_los_datos[$identificador] = $numeros;
        echo "Conjunto de datos '" . $identificador . "' agregado.\n\n";
    } else {
        echo "No se ingresaron números válidos para este conjunto. Intente de nuevo.\n\n";
    }
}

if (empty($todos_los_datos)) {
    echo "No se ingresaron conjuntos de datos. Saliendo.\n";
} else {
    echo "\nGenerando informe estadístico...\n";
    $informe_final = generarInforme($todos_los_datos);

    echo "\n--- INFORME ESTADÍSTICO ---\n";
    foreach ($informe_final as $identificador => $estadisticas) {
        echo "Conjunto: " . $identificador . "\n";
        echo "  Media: " . $estadisticas['media'] . "\n";
        echo "  Mediana: " . $estadisticas['mediana'] . "\n";
        echo "  Moda: " . (empty($estadisticas['moda']) ? 'No hay moda clara' : implode(', ', $estadisticas['moda'])) . "\n";
        echo "--------------------------\n";
    }
}