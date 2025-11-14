<?php 

require_once '/../model/Producto.php';

class HomeController{
    public function index(){
        $productoModel = new Producto();
        $productos = $productoModel->ObtenerTodos();

        //Le paso los datos a la vista.
        require __DIR__ . '/../vista/home.php';

    }
}