<?php

namespace Defaults\Models;

defined('_EXEC_APP') or die('Ups! access not allowed');

use abstracts\ModelORM;
use stdClass;
use Exception;

class DefaultModel extends ModelORM
{
    /**
     * DefaultModel constructor.
     * @param stdClass|null $properties object { server : ??, user : ??, pass : ??, db : ??, port : ??}
     */
    public function __construct(stdClass $properties = null)
    {
        parent::__construct($properties);
        $this->table = "actividades";
        $this->primary_key = "id_record";
        $this->value = 0;
        $this->find_by = "act";
    }

    /**
     *
     */
    public function getEjecucion()
    {
        $sql = "SELECT COALESCE(max(ejecucion),0) max_ejecion FROM actividades";
        return $this->query($sql)->objectList();
    }

    public function getOperations()
    {
        $sql = "SELECT id_record,name  FROM operacion";
        return $this->query($sql)->objectList();
    }

    public function setOrden($orden)
    {
        $sql = " INSERT INTO ordenes (name,pares,style_id) VALUES ('$orden->name', '$orden->pares','$orden->stock_id');  ";
        $this->query($sql)->objectList();
        $sql2 = "SELECT max(id_record) max_id FROM ordenes;";
        return $this->query($sql2)->objectList();
    }

    public function setActividad($params)
    {
        $sql = "
        INSERT INTO actividades(id_modulo,id_orden,id_problema,id_operacion,tiempo,ejecucion)
        VALUES('$params->id_modulo','$params->id_orden','$params->id_problem','$params->id_operation','$params->tiempo','$params->ejecucion')
        ";
        return $this->query($sql)->objectList();
    }

    public function getActividad($params)
    {
        $sql = "
        SELECT
          ord.id_record,
          CONCAT('Orden ',ord.name) orden,
          ord.pares,
          act.id_operacion,
          act.tiempo,
          st.cost_production, st.sell_cost,
          (st.cost_production * ord.pares) production_cost,
          (st.sell_cost * ord.pares) production_sell,
          ( select SUM(CASE WHEN act2.id_problema > 0 THEN 1 ELSE 0 END) from actividades act2 where act2.id_orden = ord.id_record ) count_time_extra,
            (
                SELECT sum(act2.tiempo)
                FROM actividades act2
                WHERE act2.id_orden = ord.id_record
            ) total_time
        FROM ordenes ord
          INNER JOIN actividades act ON act.id_orden = ord.id_record
          INNER JOIN styles st ON st.id_record = ord.style_id 
        WHERE ord.active = 1 and act.ejecucion = $params->max_ejecion and act.id_modulo = $params->id_modulo
        GROUP BY 1,2,4
        ORDER BY 1 
        ";
        return $this->query($sql)->objectList();
    }

    public function getCustomer()
    {
        $sql = " SELECT cus.id_record, cus.description FROM customer cus ";
        return $this->query($sql)->objectList();
    }

    public function getProductByCustomer($customerId)
    {
        $sql = " SELECT prod.id_record, prod.description FROM product prod where (prod.customer_id = $customerId or $customerId = 0 ); ";
        return $this->query($sql)->objectList();
    }

    public function getStock($customerId = 0, $productId = 0)
    {
        $sql = "
            SELECT st.id_record, st.description FROM styles st
            inner join product prod on prod.id_record = st.product_id
            where (st.product_id = $productId or $productId = 0 ) AND (prod.customer_id = $customerId or $customerId = 0) 
        ";
        return $this->query($sql)->objectList();
    }

    // Production ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    public function getTotalProduction()
    {
        $sql = "
        SELECT md.name description,
              SUM(CASE WHEN act.id_operacion = 1 THEN ord.pares ELSE 0 END) AS planificacion,
              SUM(CASE WHEN act.id_operacion = 2 THEN ord.pares ELSE 0 END) AS cutting,
              SUM(CASE WHEN act.id_operacion = 3 THEN ord.pares ELSE 0 END) AS handsewing,
              SUM(CASE WHEN act.id_operacion = 4 THEN ord.pares ELSE 0 END) AS horno,
              SUM(CASE WHEN act.id_operacion = 5 THEN ord.pares ELSE 0 END) AS bottoming,
              SUM(CASE WHEN act.id_operacion = 6 THEN ord.pares ELSE 0 END) AS packing,
            sum(ord.pares) total
            FROM modulo md
              INNER JOIN actividades act ON act.id_modulo = md.id_record 
              INNER JOIN ordenes ord ON ord.id_record = act.id_orden
            GROUP BY 1 
            ORDER BY 1
        ";
        return $this->query($sql)->objectList();
    }

    public function getTotalByOperation($params)
    {
        $sql = "
        SELECT
          SUM(CASE WHEN act.id_operacion = 1 THEN act.tiempo ELSE 0 END) planificacion,
          SUM(CASE WHEN act.id_operacion = 2 THEN act.tiempo ELSE 0 END) cutting,
          SUM(CASE WHEN act.id_operacion = 3 THEN act.tiempo ELSE 0 END) handsewing,
          SUM(CASE WHEN act.id_operacion = 4 THEN act.tiempo ELSE 0 END) horno,
          SUM(CASE WHEN act.id_operacion = 5 THEN act.tiempo ELSE 0 END) bottoming,
          SUM(CASE WHEN act.id_operacion = 6 THEN act.tiempo ELSE 0 END) packing
        FROM ordenes ord
          INNER JOIN actividades act ON act.id_orden = ord.id_record
        WHERE ord.active = 1 and act.ejecucion = $params->max_ejecion and act.id_modulo = $params->id_modulo;
        ";
        return $this->query($sql)->objectList();
    }

    public function getProductionByProduct(){
        $sql = "
            SELECT prod.description,
              SUM(CASE WHEN act.id_operacion = 1 THEN ord.pares ELSE 0 END) AS planificacion,
              SUM(CASE WHEN act.id_operacion = 2 THEN ord.pares ELSE 0 END) AS cutting,
              SUM(CASE WHEN act.id_operacion = 3 THEN ord.pares ELSE 0 END) AS handsewing,
              SUM(CASE WHEN act.id_operacion = 4 THEN ord.pares ELSE 0 END) AS horno,
              SUM(CASE WHEN act.id_operacion = 5 THEN ord.pares ELSE 0 END) AS bottoming,
              SUM(CASE WHEN act.id_operacion = 6 THEN ord.pares ELSE 0 END) AS packing,
            sum(ord.pares) total
            FROM product prod
              INNER JOIN styles st ON st.product_id = prod.id_record
              INNER JOIN ordenes ord ON ord.style_id = st.id_record
              INNER JOIN actividades act ON act.id_orden = ord.id_record
            GROUP BY 1 
            ORDER BY 1
        ";
        return $this->query($sql)->objectList();
    }

    public function getProductionByStyle(){
        $sql = "
            SELECT st.description,
              SUM(CASE WHEN act.id_operacion = 1 THEN ord.pares ELSE 0 END) AS planificacion,
              SUM(CASE WHEN act.id_operacion = 2 THEN ord.pares ELSE 0 END) AS cutting,
              SUM(CASE WHEN act.id_operacion = 3 THEN ord.pares ELSE 0 END) AS handsewing,
              SUM(CASE WHEN act.id_operacion = 4 THEN ord.pares ELSE 0 END) AS horno,
              SUM(CASE WHEN act.id_operacion = 5 THEN ord.pares ELSE 0 END) AS bottoming,
              SUM(CASE WHEN act.id_operacion = 6 THEN ord.pares ELSE 0 END) AS packing,
            sum(ord.pares) total
            FROM styles st
              INNER JOIN ordenes ord ON ord.style_id = st.id_record
              INNER JOIN actividades act ON act.id_orden = ord.id_record
            GROUP BY 1 
            ORDER BY 1
        ";
        return $this->query($sql)->objectList();
    }

    // Earning ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    public function getGananciaByProduct()
    {
        $sql = "
       SELECT
          a.description,
          a.cost_production,
          a.sell_cost,
          (a.sell_cost * a.pares)                                                                                                   sell_production,
          ( (a.cost_production * a.pares) + ( a.cost_production * a.problem_count) )                                                cost_production_final,
          a.problem_count                                                                                        problem_count,
          ( a.cost_production * a.problem_count) lose_earn,
          ((a.sell_cost * a.pares) - (a.cost_production * a.pares))  - (( a.cost_production * a.problem_count)) earning,
          round((( ((a.sell_cost * a.pares) - (a.cost_production * a.pares))  -  ( a.cost_production * a.problem_count) ) / ((a.cost_production * a.pares) + ( a.cost_production * a.problem_count))) * 100, 2) percent
       FROM (
           SELECT
             prod.description,
             st.cost_production,
             st.sell_cost,
             (
               SELECT SUM(CASE WHEN act.id_problema > 0 THEN 1 ELSE 0 END)
               FROM actividades act
                 inner join ordenes ord2 on ord2.id_record = act.id_orden
                 inner join styles st2 on st2.id_record = ord2.style_id
                 inner join product prod2 on prod2.id_record = st2.product_id
               WHERE prod2.id_record = prod.id_record
             )              problem_count,
             sum(ord.pares) pares
           FROM product prod
             INNER JOIN styles st ON st.product_id = prod.id_record
             INNER JOIN ordenes ord ON ord.style_id = st.id_record
           GROUP BY 1, 2, 3
       ) a
       GROUP BY 1,2,3,4,5,6
       ORDER BY 1
        ";
        return $this->query($sql)->objectList();
    }

    public function getGananciaByStyle()
    {
        $sql = "
       SELECT
          a.description,
          a.cost_production,
          a.sell_cost,
          (a.sell_cost * a.pares)                                                                             AS sell_production,
          ((a.cost_production * a.pares) + (a.cost_production *
                                            a.problem_count))                                                 AS cost_production_final,
          a.problem_count                                                                                     AS problem_count,
          (a.cost_production *
           a.problem_count)                                                                                      lose_earn,
          ((a.sell_cost * a.pares) - (a.cost_production * a.pares)) - ((a.cost_production * a.problem_count)) AS earning,
          round(((((a.sell_cost * a.pares) - (a.cost_production * a.pares)) - (a.cost_production * a.problem_count)) /
                 ((a.cost_production * a.pares) + (a.cost_production * a.problem_count))) * 100, 2)           AS percent
       FROM (
           SELECT
             st.description,
             st.cost_production,
             st.sell_cost,
             (
               SELECT SUM(CASE WHEN act.id_problema > 0
                 THEN 1
                          ELSE 0 END)
               FROM actividades act
                 inner join ordenes ord2 on ord2.id_record = act.id_orden
                 inner join styles st2 on st2.id_record = ord2.style_id
               WHERE st2.id_record = st.id_record
             ) AS           problem_count,
             sum(ord.pares) pares
           FROM styles st
             INNER JOIN ordenes ord ON ord.style_id = st.id_record
           GROUP BY 1, 2, 3
       ) a
       GROUP BY 1, 2, 3, 4, 5, 6
       ORDER BY 1
        ";
        return $this->query($sql)->objectList();
    }

    public function getGananciaByModule(){
        $sql = "
        SELECT
          a.description,
          a.cost_production,
          a.sell_cost,
          (a.sell_cost * a.pares)                                                                             AS sell_production,
          ((a.cost_production * a.pares) + (a.cost_production *
                                            a.problem_count))                                                 AS cost_production_final,
          a.problem_count                                                                                     AS problem_count,
          (a.cost_production *
           a.problem_count)                                                                                      lose_earn,
          ((a.sell_cost * a.pares) - (a.cost_production * a.pares)) - ((a.cost_production * a.problem_count)) AS earning,
          round(((((a.sell_cost * a.pares) - (a.cost_production * a.pares)) - (a.cost_production * a.problem_count)) /
                 ((a.cost_production * a.pares) + (a.cost_production * a.problem_count))) * 100, 2)           AS percent
        FROM (
               SELECT
                 md.name AS description,
                 st.cost_production cost_production,
                 st.sell_cost sell_cost,
                 (
                   SELECT SUM(CASE WHEN act2.id_problema > 0 THEN 1 ELSE 0 END)
                   FROM actividades act2
                     inner join ordenes ord2 on ord2.id_record = act2.id_orden
                     inner join styles st2 on st2.id_record = ord2.style_id
                   WHERE act2.id_modulo = md.id_record
                 ) AS           problem_count,
                 sum(ord.pares) pares
               FROM styles st
                 INNER JOIN ordenes ord ON ord.style_id = st.id_record
                 INNER JOIN (select id_modulo,id_orden from actividades group by 1, 2) act ON act.id_orden = ord.id_record
                 INNER JOIN modulo md ON md.id_record = act.id_modulo
               GROUP BY 1
             ) a
        GROUP BY 1, 2, 3, 4, 5, 6
        ORDER BY 1
        ";
        return $this->query($sql)->objectList();
    }

    // Time ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    public function getTiempoByProduct()
    {
        $sql = "        
        SELECT
          a.product_name,
          a.count_product,
          a.product_time,
          round((a.product_time / a.count_product), 3)    AS product_promedio,
          round((a.product_time / a.total_time) * 100, 3) AS percent
        FROM (
               SELECT
                 prod.description          AS product_name,
                 count(prod.id_record)     AS count_product,
                 round(sum(act.tiempo), 2) AS product_time,
                 (
                   select round(sum(act2.tiempo), 2)
                   from actividades act2
                 )                         AS total_time
               FROM product prod
                 INNER JOIN styles st ON st.product_id = prod.id_record
                 INNER JOIN ordenes ord ON ord.style_id = st.id_record
                 INNER JOIN actividades act ON act.id_orden = ord.id_record
               WHERE TRUE
               GROUP BY 1
               ORDER BY 2 DESC
             ) a
        ORDER BY 1
        LIMIT 10;
        ";
        return $this->query($sql)->objectList();
    }

    public function getTiempoByStyle(){
        $sql = "
        SELECT
          a.style_name,
          a.count_style,
          a.style_time,
          round((a.style_time / a.count_style), 3)    AS style_promedio,
          round((a.style_time / a.total_time) * 100, 3) AS percent
        FROM (
               SELECT
                 st.description          AS style_name,
                 count(st.id_record)     AS count_style,
                 round(sum(act.tiempo), 2) AS style_time,
                 (
                   select round(sum(act2.tiempo), 2)
                   from actividades act2
                 )                         AS total_time
               FROM  styles st
                 INNER JOIN ordenes ord ON ord.style_id = st.id_record
                 INNER JOIN actividades act ON act.id_orden = ord.id_record
               WHERE TRUE
               GROUP BY 1
               ORDER BY 2 DESC
             ) a
        ORDER BY 1
        LIMIT 10;
        ";
        return $this->query($sql)->objectList();
    }

    public function getTiempoByModule()
    {
        $sql = "
        SELECT
          a.module,
          a.modulo_time,
          a.count_module,
          round((a.modulo_time / a.count_module), 3)            AS module_promedio,
          round((a.modulo_time / a.total_module_time) * 100, 3) AS percent
        FROM (
               SELECT
                 md.name                   AS module,
                 round(sum(act.tiempo), 2) AS modulo_time,
                 count(act.id_modulo)      as count_module,
                 (
                   select round(sum(act2.tiempo))
                   from actividades act2
                 )                         AS total_module_time
               FROM actividades act
                 INNER JOIN modulo md ON md.id_record = act.id_modulo
               where act.active = 1
               group by 1
             ) a
             ORDER BY 1;
        ";
        return $this->query($sql)->objectList();
    }

    // Problem :::::::::::::::::::::::::::::::::::::::::::::::::::::::
    public function getTotalProblemsByEjection($params)
    {
        $sql = "
        SELECT
           act.id_problema,
          pro.descripcion problema,
          count(*)        cant_problema,
          (
            SELECT sum(a.cant_problema) sum_problema
            FROM (
                   SELECT count(*) cant_problema
                   FROM actividades act
                     INNER JOIN problema pro ON pro.id_record = act.id_problema AND pro.id_operacion = act.id_operacion
                     INNER JOIN solucion sol ON sol.id_record = pro.id_solucion
                   WHERE act.id_problema != 0 and act.ejecucion = $params
                   GROUP BY act.id_problema
                 ) a
          )               sum_problem
        FROM actividades act
          INNER JOIN problema pro ON pro.id_record = act.id_problema AND pro.id_operacion = act.id_operacion
          INNER JOIN solucion sol ON sol.id_record = pro.id_solucion
        WHERE act.id_problema != 0  and act.ejecucion = $params
        GROUP BY 1;
        ";
        return $this->query($sql)->objectList();
    }

    public function getTotalProblems()
    {
        $sql = "
        SELECT
          act.id_problema,
          pro.descripcion        problema,
          sol.descripcion        solucion,
          count(*) cant_problema
        FROM actividades act
          INNER JOIN problema pro ON pro.id_record = act.id_problema AND pro.id_operacion = act.id_operacion
          INNER JOIN solucion sol ON sol.id_record = pro.id_solucion
        WHERE act.id_problema != 0 
        GROUP BY 1;
        ";
        return $this->query($sql)->objectList();
    }

    public function getProblemByProduct()
    {
        $sql = "
        SELECT
          prod.description       AS product_name,
          count(act.id_problema) AS count_problem
        FROM product prod
          INNER JOIN styles st ON st.product_id = prod.id_record
          INNER JOIN ordenes ord ON ord.style_id = st.id_record
          INNER JOIN actividades act ON act.id_orden = ord.id_record
        WHERE act.id_problema > 0
        GROUP BY 1
        ORDER BY 1;
        ";
        return $this->query($sql)->objectList();
    }

    public function getProblemByStyle()
    {
        $sql = "
        SELECT
          st.description       AS style_name,
          count(act.id_problema) AS count_problem
        FROM  styles st
          INNER JOIN ordenes ord ON ord.style_id = st.id_record
          INNER JOIN actividades act ON act.id_orden = ord.id_record
        WHERE act.id_problema > 0
        GROUP BY 1
        ORDER BY 1;
        ";
        return $this->query($sql)->objectList();
    }

    public function getProblemsModule($params)
    {
        $sql = "
        SELECT
          act.id_modulo,
          act.id_problema,
          pro.descripcion problema,
          count(*)        cant_problema
        FROM actividades act
          INNER JOIN problema pro ON pro.id_record = act.id_problema AND pro.id_operacion = act.id_operacion
          INNER JOIN solucion sol ON sol.id_record = pro.id_solucion
        WHERE act.id_problema != 0 and act.ejecucion = $params
        GROUP BY 1,2
        ";
        return $this->query($sql)->objectList();
    }

    public function getProblemByModel()
    {
        $sql = "
        SELECT
          md.name                AS module_name,
          count(act.id_problema) AS count_problem
        FROM actividades act
          INNER JOIN modulo md ON md.id_record = act.id_modulo
        WHERE act.id_problema > 0
        GROUP BY 1
        ORDER BY 1;
        ";
        return $this->query($sql)->objectList();
    }

    public function getOperationsProblems($id_operation)
    {
        $sql = "select id_record from problema where id_operacion =  $id_operation";
        return $this->query($sql)->objectList();
    }
}
