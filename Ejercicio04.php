<?php

abstract class MatrizAbstracta {
    abstract public function multiplicar(array $matriz_b): array; 
    abstract public function inversa(): array; 
}

class Matriz extends MatrizAbstracta {
    private array $elementos; 

    public function __construct(array $elementos) {
        if (empty($elementos)) {
            $this->elementos = [];
            return;
        }

        $num_filas = count($elementos);
        $num_columnas = count(reset($elementos)); 

        foreach ($elementos as $fila_idx => $fila) {
            if (!is_array($fila) || count($fila) !== $num_columnas) {
                throw new InvalidArgumentException("La fila " . $fila_idx . " no tiene el número correcto de columnas o no es un array.");
            }
            foreach ($fila as $col_idx => $valor) {
                if (!is_numeric($valor)) {
                    throw new InvalidArgumentException("El elemento [" . $fila_idx . "][" . $col_idx . "] no es numérico.");
                }
                $this->elementos['fila' . $fila_idx]['col' . $col_idx] = (float) $valor;
            }
        }
    }

    public function getElementos(): array {
       
        $simple_array = [];
        foreach ($this->elementos as $fila_key => $fila_val) {
            $fila_num = (int) substr($fila_key, 4); 
            foreach ($fila_val as $col_key => $col_val) {
                $col_num = (int) substr($col_key, 3); 
                $simple_array[$fila_num][$col_num] = $col_val;
            }
        }

        return array_values(array_map('array_values', $simple_array));
    }

    public function multiplicar(array $matriz_b_array): array {
        $matriz_a = $this->getElementos(); 

        if (empty($matriz_a) || empty($matriz_b_array)) {
            return [];
        }

        $filas_a = count($matriz_a);
        $cols_a = count($matriz_a[0]);
        $filas_b = count($matriz_b_array);
        $cols_b = count($matriz_b_array[0]);

        if ($cols_a !== $filas_b) {
            throw new InvalidArgumentException("Las dimensiones de las matrices no son compatibles para la multiplicación.");
        }

        $resultado = array_fill(0, $filas_a, array_fill(0, $cols_b, 0.0));

        for ($i = 0; $i < $filas_a; $i++) {
            for ($j = 0; $j < $cols_b; $j++) {
                for ($k = 0; $k < $cols_a; $k++) {
                    $resultado[$i][$j] += $matriz_a[$i][$k] * $matriz_b_array[$k][$j];
                }
            }
        }
        return $resultado;
    }

    public function inversa(): array {
        $elementos = $this->getElementos();
        $filas = count($elementos);
        $cols = count($elementos[0]);

        if ($filas !== $cols) {
            throw new InvalidArgumentException("Solo se puede calcular la inversa de matrices cuadradas.");
        }

        if ($filas === 2) {
            $a = $elementos[0][0];
            $b = $elementos[0][1];
            $c = $elementos[1][0];
            $d = $elementos[1][1];

            $determinante_val = ($a * $d) - ($b * $c);

            if ($determinante_val == 0) {
                throw new InvalidArgumentException("La matriz no tiene inversa (determinante es cero).");
            }

            $factor = 1 / $determinante_val;

            $inversa_2x2 = [];
            $inversa_2x2[0][0] = $d * $factor;
            $inversa_2x2[0][1] = -$b * $factor;
            $inversa_2x2[1][0] = -$c * $factor;
            $inversa_2x2[1][1] = $a * $factor;

            return $inversa_2x2;

        } elseif ($filas > 2) {
            echo "Advertencia: La implementación completa de la inversa para matrices de tamaño > 2x2 es extensa y no está incluida aquí.\n";
            return ['error' => 'Inversa para matrices >2x2 no implementada completamente en este ejemplo.'];
        } else {
            $valor = $elementos[0][0];
            if ($valor == 0) {
                 throw new InvalidArgumentException("La matriz 1x1 no tiene inversa (valor es cero).");
            }
            return [[1 / $valor]];
        }
    }
}

function determinante(array $matriz_elementos): float {
    $n = count($matriz_elementos);

    if ($n === 0) {
        return 0.0; 
    }

    foreach ($matriz_elementos as $fila) {
        if (count($fila) !== $n) {
            throw new InvalidArgumentException("La matriz no es cuadrada. No se puede calcular el determinante.");
        }
    }

    if ($n === 1) {
        return $matriz_elementos[0][0];
    } elseif ($n === 2) {
        return ($matriz_elementos[0][0] * $matriz_elementos[1][1]) - ($matriz_elementos[0][1] * $matriz_elementos[1][0]);
    } else {
        $det = 0.0;
        for ($j = 0; $j < $n; $j++) { 
            $menor = obtenerMenor($matriz_elementos, 0, $j);
            $signo = ($j % 2 === 0) ? 1 : -1;
            $det += $signo * $matriz_elementos[0][$j] * determinante($menor);
        }
        return $det;
    }
}

function obtenerMenor(array $matriz, int $fila_omitir, int $col_omitir): array {
    $menor = [];
    $num_filas = count($matriz);
    $num_cols = count($matriz[0]);

    $fila_nueva = 0;
    for ($i = 0; $i < $num_filas; $i++) {
        if ($i === $fila_omitir) continue;

        $col_nueva = 0;
        for ($j = 0; $j < $num_cols; $j++) {
            if ($j === $col_omitir) continue;
            $menor[$fila_nueva][$col_nueva] = $matriz[$i][$j];
            $col_nueva++;
        }
        $fila_nueva++;
    }
    return $menor;
}

function leerMatrizDelUsuario(string $nombre_matriz): array {
    echo "Ingrese la matriz " . $nombre_matriz . ".\n";
    echo "Cuántas filas tiene la matriz? ";
    $filas = (int) trim(fgets(STDIN));

    if ($filas <= 0) {
        echo "El número de filas debe ser positivo.\n";
        return [];
    }

    echo "Cuántas columnas tiene la matriz? ";
    $columnas = (int) trim(fgets(STDIN));

    if ($columnas <= 0) {
        echo "El número de columnas debe ser positivo.\n";
        return [];
    }

    $matriz_usuario = [];
    for ($i = 0; $i < $filas; $i++) {
        echo "Ingrese los " . $columnas . " elementos de la fila " . ($i + 1) . " separados por espacios (ej. 1 2 3): ";
        $fila_str = trim(fgets(STDIN));
        $elementos_fila = explode(' ', $fila_str);

        if (count($elementos_fila) !== $columnas) {
            echo "Error: Debe ingresar exactamente " . $columnas . " elementos. Intente de nuevo esta fila.\n";
            $i--; 
            continue;
        }

        $fila_valida = [];
        foreach ($elementos_fila as $elem) {
            $elem_limpio = trim($elem);
            if (is_numeric($elem_limpio)) {
                $fila_valida[] = (float) $elem_limpio;
            } else {
                echo "Error: Todos los elementos deben ser numéricos. Intente de nuevo esta fila.\n";
                $i--;
                continue 2; 
            }
        }
        $matriz_usuario[] = $fila_valida;
    }
    return $matriz_usuario;
}

function imprimirMatriz(array $matriz, string $nombre = "Matriz"): void {
    echo $nombre . ":\n";
    if (empty($matriz)) {
        echo "[]\n";
        return;
    }
    foreach ($matriz as $fila) {
        echo "[ " . implode(" ", $fila) . " ]\n";
    }
}


echo "--- Operaciones con Matrices ---\n\n";

try {
    $matriz_a_arr = leerMatrizDelUsuario("Matriz A");
    if (empty($matriz_a_arr)) {
        echo "No se pudo crear la Matriz A. Saliendo.\n";
        exit;
    }
    $matriz_a = new Matriz($matriz_a_arr);
    imprimirMatriz($matriz_a->getElementos(), "Matriz A");

    echo "\n";

    echo "Calculando el determinante de A...\n";
    try {
        $det_a = determinante($matriz_a->getElementos());
        echo "Determinante de A: " . $det_a . "\n";
    } catch (InvalidArgumentException $e) {
        echo "Error al calcular determinante: " . $e->getMessage() . "\n";
    }

    echo "\n";

    echo "Calculando la inversa de A...\n";
    try {
        $inversa_a = $matriz_a->inversa();
        if (!isset($inversa_a['error'])) {
            imprimirMatriz($inversa_a, "Inversa de A");
        } else {
            echo $inversa_a['error'] . "\n";
        }
    } catch (InvalidArgumentException $e) {
        echo "Error al calcular inversa: " . $e->getMessage() . "\n";
    }

    echo "\n";

    echo "Ahora ingrese la Matriz B para la multiplicación (debe ser compatible con A).\n";
    $matriz_b_arr = leerMatrizDelUsuario("Matriz B");
    if (empty($matriz_b_arr)) {
        echo "No se pudo crear la Matriz B. No se realizará la multiplicación.\n";
    } else {
        $matriz_b = new Matriz($matriz_b_arr);
        imprimirMatriz($matriz_b->getElementos(), "Matriz B");
        echo "\n";

        echo "Calculando la multiplicación A * B...\n";
        try {
            $producto_ab = $matriz_a->multiplicar($matriz_b->getElementos());
            imprimirMatriz($producto_ab, "A * B");
        } catch (InvalidArgumentException $e) {
            echo "Error en la multiplicación: " . $e->getMessage() . "\n";
        }
    }

} catch (InvalidArgumentException $e) {
    echo "Error fatal: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Ocurrió un error inesperado: " . $e->getMessage() . "\n";
}
