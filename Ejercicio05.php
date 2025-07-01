<?php


// Clase abstracta que define la estructura para resolver ecuaciones diferenciales
abstract class EcuacionDiferencial {
    // Método abstracto que debe implementar el método de Euler
    abstract public function resolverEuler(
        callable $funcion_diferencial, 
        float $x0, 
        float $y0, 
        float $h, 
        float $x_final
    ): array;
}

// Clase que implementa el método de Euler para resolver ecuaciones diferenciales
class EulerNumerico extends EcuacionDiferencial {
    
    public function resolverEuler(
        callable $funcion_diferencial,
        float $x0,
        float $y0,
        float $h,
        float $x_final
    ): array {
        $solucion = []; // Arreglo para almacenar los resultados
        $x = $x0;       // Valor inicial de x
        $y = $y0;       // Valor inicial de y

        $solucion[(string)$x0] = $y0; // Guarda el valor inicial

        // Calcula el número de pasos necesarios
        $num_pasos = (int)ceil(($x_final - $x0) / $h);

        // Bucle principal del método de Euler
        for ($i = 0; $i < $num_pasos; $i++) {

            // Calcula la derivada en el punto actual
            $dy_dx = call_user_func($funcion_diferencial, $x, $y);

            // Calcula el siguiente valor de y usando Euler
            $y_siguiente = $y + ($h * $dy_dx);

            // Calcula el siguiente valor de x
            $x_siguiente = $x + $h;

            // Ajusta el último paso si se pasa del valor final de x
            if ($x_siguiente > $x_final && $x < $x_final) {
                $h_ajustado = $x_final - $x;
                $y_siguiente = $y + ($h_ajustado * $dy_dx);
                $x_siguiente = $x_final;
            }

            // Guarda el resultado en el arreglo
            $solucion[(string)$x_siguiente] = $y_siguiente;

            // Actualiza x e y para el siguiente paso
            $x = $x_siguiente;
            $y = $y_siguiente;

            // Si se alcanzó o superó el valor final de x, termina el bucle
            if ($x >= $x_final) {
                break;
            }
        }
        return $solucion; // Devuelve la solución aproximada
    }
}

// Función para aplicar el método de Euler usando los parámetros dados
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

// Ejemplo de ecuación diferencial: dy/dx = x + y
function miEcuacionDiferencial(float $x, float $y): float {
    return $x + $y;
}

// Ejemplo de ecuación diferencial: dy/dx = -2y
function otraEcuacionDiferencial(float $x, float $y): float {
    return -2 * $y;
}

// --- Interfaz de usuario por consola ---

echo "--- Resolución de Ecuaciones Diferenciales con el Método de Euler ---\n\n";

echo "Seleccione una ecuación diferencial de ejemplo:\n";
echo "1. dy/dx = x + y\n";
echo "2. dy/dx = -2y\n";
echo "Ingrese el número de la opción deseada: ";
$opcion_ecuacion = (int) trim(fgets(STDIN));

// Selecciona la ecuación diferencial según la opción del usuario
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

// Solicita el valor inicial de x
echo "Valor inicial de x (x0): ";
$x0_usuario = (float) trim(fgets(STDIN));

// Solicita el valor inicial de y
echo "Valor inicial de y (y0): ";
$y0_usuario = (float) trim(fgets(STDIN));

// Solicita el tamaño del paso
echo "Tamaño del paso (h, ej. 0.1): ";
$h_usuario = (float) trim(fgets(STDIN));
if ($h_usuario <= 0) {
    echo "El tamaño del paso debe ser positivo. Usando 0.1 por defecto.\n";
    $h_usuario = 0.1;
}

// Solicita el valor final de x
echo "Valor final de x (x_final, ej. 1.0): ";
$x_final_usuario = (float) trim(fgets(STDIN));
if ($x_final_usuario < $x0_usuario) {
    echo "El valor final de x no puede ser menor que el inicial. Usando x0 + 1 por defecto.\n";
    $x_final_usuario = $x0_usuario + 1.0;
}

// Arreglos con las condiciones iniciales y parámetros del método
$condiciones_iniciales_usuario = [
    'x0' => $x0_usuario,
    'y0' => $y0_usuario
];

$parametros_metodo_usuario = [
    'h' => $h_usuario,
    'x_final' => $x_final_usuario
];

echo "\nCalculando la solución aproximada...\n";

// Llama a la función para resolver la ecuación diferencial
$solucion_aproximada = aplicarMetodo(
    $ecuacion_seleccionada,
    $condiciones_iniciales_usuario,
    $parametros_metodo_usuario
);

// Muestra los resultados
echo "\n--- Solución Aproximada (Valores y en función de x) ---\n";
foreach ($solucion_aproximada as $x_val_str => $y_val) {
    $x_val = (float) $x_val_str;
    echo sprintf("x = %.4f, y = %.4f\n", $x_val, $y_val);
}

