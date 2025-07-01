<?php

abstract class EcuacionDiferencial {
    abstract public function resolverEuler(
        callable $funcion_diferencial, 
        float $x0, 
        float $y0, 
        float $h, 
        float $x_final
    ): array;
}


class EulerNumerico extends EcuacionDiferencial {
    
    public function resolverEuler(
        callable $funcion_diferencial,
        float $x0,
        float $y0,
        float $h,
        float $x_final
    ): array {
        $solucion = [];
        $x = $x0;
        $y = $y0;


        $solucion[(string)$x0] = $y0; 


        $num_pasos = (int)ceil(($x_final - $x0) / $h);

        for ($i = 0; $i < $num_pasos; $i++) {

            $dy_dx = call_user_func($funcion_diferencial, $x, $y);


            $y_siguiente = $y + ($h * $dy_dx);


            $x_siguiente = $x + $h;


            if ($x_siguiente > $x_final && $x < $x_final) {

                $h_ajustado = $x_final - $x;
                $y_siguiente = $y + ($h_ajustado * $dy_dx);
                $x_siguiente = $x_final;
            }


            $solucion[(string)$x_siguiente] = $y_siguiente;


            $x = $x_siguiente;
            $y = $y_siguiente;


            if ($x >= $x_final) {
                break;
            }
        }
        return $solucion;
    }
}


function aplicarMetodo(
    callable $ecuacion_diferencial,
    array $condiciones_iniciales,
    array $parametros_metodo
): array {
    $x0 = $condiciones_iniciales['x0'] ?? 0.0;
    $y0 = $condiciones_iniciales['y0'] ?? 0.0;
    $h = $parametros_metodo['h'] ?? 0.1;
    $x_final = $parametros_metodo['x_final'] ?? 1.0;

    $euler_solver = new EulerNumerico();
    return $euler_solver->resolverEuler($ecuacion_diferencial, $x0, $y0, $h, $x_final);
}


function miEcuacionDiferencial(float $x, float $y): float {
    return $x + $y;
}


function otraEcuacionDiferencial(float $x, float $y): float {
    return -2 * $y;
}




echo "--- Resolución de Ecuaciones Diferenciales con el Método de Euler ---\n\n";

echo "Seleccione una ecuación diferencial de ejemplo:\n";
echo "1. dy/dx = x + y\n";
echo "2. dy/dx = -2y\n";
echo "Ingrese el número de la opción deseada: ";
$opcion_ecuacion = (int) trim(fgets(STDIN));

$ecuacion_seleccionada = null;
switch ($opcion_ecuacion) {
    case 1:
        $ecuacion_seleccionada = 'miEcuacionDiferencial';
        echo "Ha seleccionado: dy/dx = x + y\n";
        break;
    case 2:
        $ecuacion_seleccionada = 'otraEcuacionDiferencial';
        echo "Ha seleccionado: dy/dx = -2y\n";
        break;
    default:
        echo "Opción inválida. Usando la ecuación por defecto: dy/dx = x + y\n";
        $ecuacion_seleccionada = 'miEcuacionDiferencial';
        break;
}

echo "\n--- Ingrese las condiciones iniciales y parámetros del método ---\n";

echo "Valor inicial de x (x0): ";
$x0_usuario = (float) trim(fgets(STDIN));

echo "Valor inicial de y (y0): ";
$y0_usuario = (float) trim(fgets(STDIN));

echo "Tamaño del paso (h, ej. 0.1): ";
$h_usuario = (float) trim(fgets(STDIN));
if ($h_usuario <= 0) {
    echo "El tamaño del paso debe ser positivo. Usando 0.1 por defecto.\n";
    $h_usuario = 0.1;
}

echo "Valor final de x (x_final, ej. 1.0): ";
$x_final_usuario = (float) trim(fgets(STDIN));
if ($x_final_usuario < $x0_usuario) {
    echo "El valor final de x no puede ser menor que el inicial. Usando x0 + 1 por defecto.\n";
    $x_final_usuario = $x0_usuario + 1.0;
}


$condiciones_iniciales_usuario = [
    'x0' => $x0_usuario,
    'y0' => $y0_usuario
];

$parametros_metodo_usuario = [
    'h' => $h_usuario,
    'x_final' => $x_final_usuario
];

echo "\nCalculando la solución aproximada...\n";

$solucion_aproximada = aplicarMetodo(
    $ecuacion_seleccionada,
    $condiciones_iniciales_usuario,
    $parametros_metodo_usuario
);

echo "\n--- Solución Aproximada (Valores y en función de x) ---\n";
foreach ($solucion_aproximada as $x_val_str => $y_val) {
    $x_val = (float) $x_val_str;
    echo sprintf("x = %.4f, y = %.4f\n", $x_val, $y_val);
}

?>