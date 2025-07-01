<?php

abstract class SistemaEcuaciones {
    abstract public function calcularResultado(array $ecuacion1, array $ecuacion2): array;
    abstract public function validarConsistencia(array $ecuacion1, array $ecuacion2): bool;
}

class SistemaLineal extends SistemaEcuaciones {
    public function calcularResultado(array $ecuacion1, array $ecuacion2): array {

        $a1 = $ecuacion1['x'] ?? 0;  
        $b1 = $ecuacion1['y'] ?? 0;
        $c1 = $ecuacion1['independiente'] ?? 0;

        $a2 = $ecuacion2['x'] ?? 0;
        $b2 = $ecuacion2['y'] ?? 0;
        $c2 = $ecuacion2['independiente'] ?? 0;

        $denominador_y = ($a1 * $b2) - ($a2 * $b1);

        //analiza que el denominador no sea cero
        if ($denominador_y == 0) {
            if (($a1 * $c2) - ($a2 * $c1) == 0) {
                return ['mensaje' => 'El sistema tiene infinitas soluciones o las ecuaciones son dependientes.'];
            } else {
                return ['mensaje' => 'El sistema no tiene solución.'];
            }
        }

        //obtiene el valor de Y
        $y = (($a1 * $c2) - ($a2 * $c1)) / $denominador_y;

        //analiza cual es el valor indicado de X
        if ($a1 != 0) {
            $x = ($c1 - ($b1 * $y)) / $a1;
        } elseif ($a2 != 0) {
            $x = ($c2 - ($b2 * $y)) / $a2;
        } else {
            return ['mensaje' => 'No se puede despejar x.'];
        }

        //devuelve los valores para impresion en un array asociado
        return ['x' => $x, 'y' => $y];
    }

    public function validarConsistencia(array $ecuacion1, array $ecuacion2): bool {
        //revisa que los valores dentro de las ecuaciones exitan y dado caso los tome como valor 0
        $a1 = $ecuacion1['x'] ?? 0;
        $b1 = $ecuacion1['y'] ?? 0;
        $a2 = $ecuacion2['x'] ?? 0;
        $b2 = $ecuacion2['y'] ?? 0;

        $determinante = ($a1 * $b2) - ($a2 * $b1);

        return $determinante != 0;
    }
}

function resolverSistema(array $ecuacion1, array $ecuacion2): array {
    
    $sistema = new SistemaLineal();

    if ($sistema->validarConsistencia($ecuacion1, $ecuacion2)) {
        return $sistema->calcularResultado($ecuacion1, $ecuacion2);
    } else {
        return $sistema->calcularResultado($ecuacion1, $ecuacion2);
    }
}


echo "Ingrese los coeficientes para la primera ecuación: \n";
echo "Coeficiente de x: ";
$a1 = (float) trim(fgets(STDIN)); 
echo "Coeficiente de y: ";
$b1 = (float) trim(fgets(STDIN));
echo "Término independiente: ";
$c1 = (float) trim(fgets(STDIN));

$ecuacion1_usuario = [
    'x' => $a1,
    'y' => $b1,
    'independiente' => $c1
];

echo "\nIngrese los coeficientes para la segunda ecuación:\n";
echo "Coeficiente de x: ";
$a2 = (float) trim(fgets(STDIN));
echo "Coeficiente de y: ";
$b2 = (float) trim(fgets(STDIN));
echo "Término independiente: ";
$c2 = (float) trim(fgets(STDIN));

$ecuacion2_usuario = [
    'x' => $a2,
    'y' => $b2,
    'independiente' => $c2
];


$soluciones = resolverSistema($ecuacion1_usuario, $ecuacion2_usuario);

if (isset($soluciones['x']) && isset($soluciones['y'])) {  
    echo "Las soluciones son:\n";
    echo "x = " . $soluciones['x'] . "\n";
    echo "y = " . $soluciones['y'] . "\n";
} else {
    echo "Mensaje: " . $soluciones['mensaje'] . "\n";
}
