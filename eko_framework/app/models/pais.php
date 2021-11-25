<?php
class PaisModel extends Model{
    var $name = 'Pais';  //ESTE VALOR DEBO OBTENERLO DINAMICAMENTE, POR LO PRONTO QUE ASI SEA PARIENTES!!
    var $useTable="cat_paises";
    var $primaryKey = 'id_pai';
    var $specific = true;
    var $camposAfiltrar = array('nom_pai');
}
?>
