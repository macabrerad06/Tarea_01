<?php

// Clase abstracta que define métodos básicos para trabajar con polinomios
abstract class PolinomioAbstracto {
    abstract public function evaluar(float $x): float;
    abstract public function derivada(): array; 
}

// Implementación concreta del polinomio
class Polinomio extends PolinomioAbstracto {
    private array $terminos;

    public function __construct(array $terminos) {
        $this->terminos = $this->limpiarPolinomio($terminos);
    }

    // Elimina términos con coeficiente cero y ordena de mayor a menor grado
    private function limpiarPolinomio(array $terminos_raw): array {
        $limpio = [];
        foreach ($terminos_raw as $grado => $coeficiente) {
            $grado = (int) $grado;
            $coeficiente = (float) $coeficiente;
            if ($coeficiente !== 0.0 || $grado === 0) {
                $limpio[$grado] = $coeficiente;
            }
        }
        krsort($limpio);
        return $limpio;
    }

    public function getTerminos(): array {
        return $this->terminos;
    }

    // Evalúa el polinomio en un valor dado de x
    public function evaluar(float $x): float {
        $resultado = 0.0;
        foreach ($this->terminos as $grado => $coeficiente) {
            $resultado += $coeficiente * ($x ** $grado); 
        }
        return $resultado;
    }

    // Devuelve la derivada del polinomio como nuevo conjunto de términos
    public function derivada(): array {
        $derivada_terminos = [];
        foreach ($this->terminos as $grado => $coeficiente) {
            if ($grado > 0) {
                $nuevo_grado = $grado - 1;
                $nuevo_coeficiente = $coeficiente * $grado;
                $derivada_terminos[$nuevo_grado] = $nuevo_coeficiente;
            }
        }
        krsort($derivada_terminos);
        return $derivada_terminos;
    }
}

// Suma dos polinomios representados como arrays de términos
function sumarPolinomios(array $polinomio1_terminos, array $polinomio2_terminos): array {
    $suma_terminos = [];

    foreach ($polinomio1_terminos as $grado => $coeficiente) {
        $suma_terminos[$grado] = ($suma_terminos[$grado] ?? 0.0) + $coeficiente;
    }
    foreach ($polinomio2_terminos as $grado => $coeficiente) {
        $suma_terminos[$grado] = ($suma_terminos[$grado] ?? 0.0) + $coeficiente;
    }

    $polinomio_suma_limpio = [];
    foreach ($suma_terminos as $grado => $coeficiente) {
        if ($coeficiente !== 0.0 || $grado === 0) {
            $polinomio_suma_limpio[$grado] = $coeficiente;
        }
    }

    krsort($polinomio_suma_limpio);
    return $polinomio_suma_limpio;
}

// Permite al usuario ingresar términos de un polinomio desde consola
function leerPolinomioDelUsuario(string $nombre_polinomio): array {
    $terminos = [];
    echo "Ingrese los términos para el " . $nombre_polinomio . " (ej. 'grado,coeficiente'). Escriba 'fin' para terminar.\n";

    while (true) {
        echo "Término (grado,coeficiente) o 'fin': ";
        $entrada = trim(fgets(STDIN));

        if (strtolower($entrada) === 'fin') break;

        $partes = explode(',', $entrada);
        if (count($partes) === 2 && is_numeric($partes[0]) && is_numeric($partes[1])) {
            $grado = (int) trim($partes[0]);
            $coeficiente = (float) trim($partes[1]);
            $terminos[$grado] = ($terminos[$grado] ?? 0.0) + $coeficiente;
            echo "Término '" . $coeficiente . "x^" . $grado . "' agregado.\n";
        } else {
            echo "Entrada inválida. Por favor, use el formato 'grado,coeficiente'.\n";
        }
    }
    return $terminos;
}

// Imprime un polinomio formateado como cadena legible
function imprimirPolinomio(array $terminos, string $nombre = "Polinomio"): void {
    if (empty($terminos)) {
        echo $nombre . ": 0\n";
        return;
    }

    $polinomio_str = [];
    krsort($terminos); 
    foreach ($terminos as $grado => $coeficiente) {
        if ($coeficiente == 0 && $grado != 0) continue;

        $signo = $coeficiente >= 0 ? '+' : '-';
        $abs_coeficiente = abs($coeficiente);

        if ($grado === 0) {
            $polinomio_str[] = ($coeficiente >= 0 ? ($polinomio_str ? '+' : '') : '-') . $abs_coeficiente;
        } elseif ($grado === 1) {
            $polinomio_str[] = ($polinomio_str ? $signo : '') . ($abs_coeficiente == 1 ? '' : $abs_coeficiente) . 'x';
        } else {
            $polinomio_str[] = ($polinomio_str ? $signo : '') . ($abs_coeficiente == 1 ? '' : $abs_coeficiente) . 'x^' . $grado;
        }
    }

    if (!empty($polinomio_str) && substr($polinomio_str[0], 0, 1) === '+') {
        $polinomio_str[0] = substr($polinomio_str[0], 1);
    }

    if (empty($polinomio_str)) {
        echo $nombre . ": 0\n";
        return;
    }

    echo $nombre . ": " . implode(' ', $polinomio_str) . "\n";
}

// Programa principal de prueba
echo "--- Manejo de Polinomios ---\n\n";

$terminos_polinomio1 = leerPolinomioDelUsuario("primer polinomio");
$p1 = new Polinomio($terminos_polinomio1);
imprimirPolinomio($p1->getTerminos(), "P1");

echo "\n";

$terminos_polinomio2 = leerPolinomioDelUsuario("segundo polinomio");
$p2 = new Polinomio($terminos_polinomio2);
imprimirPolinomio($p2->getTerminos(), "P2");

echo "\n";

echo "Calculando la suma de P1 y P2...\n";
$terminos_suma = sumarPolinomios($p1->getTerminos(), $p2->getTerminos());
imprimirPolinomio($terminos_suma, "P1 + P2");

echo "\n";

echo "Evaluación de P1:\n";
echo "Ingrese el valor de x para evaluar P1: ";
$x_evaluar = (float) trim(fgets(STDIN));
$resultado_evaluacion = $p1->evaluar($x_evaluar);
echo "P1(" . $x_evaluar . ") = " . $resultado_evaluacion . "\n";

echo "\n";

echo "Calculando la derivada de P1...\n";
$terminos_derivada = $p1->derivada();
imprimirPolinomio($terminos_derivada, "P1'");
