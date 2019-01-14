<?php
/**
 * Created by PhpStorm.
 * User: taishiro
 * Date: 1/14/19
 * Time: 3:02 PM
 */

namespace App\Repositories;


use Illuminate\Http\Request;

interface RepositoriesInterface
{
    public function selectAll();

    public function find($id);

    public function create(Request $request);


}