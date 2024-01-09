<?php

namespace App\Interface;

use App\Models\PontoUsuario;

interface IPontoUsuarioRepository
{
    public function Get($id, $joins = [], $columns = ['*']);
    public function Delete($id);
    public function Update($id, array $newData);
    public function Create(array $data);
}