<?php

class Entity
{
    public $ID;
    public $Parametr;
    public $Health;
    public $Damage;

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->ID;
    }

    /**
     * @return mixed
     */
    public function getParametr()
    {
        return $this->Parametr;
    }

    /**
     * @return mixed
     */
    public function getHealth()
    {
        return $this->Health;
    }

    /**
     * @return mixed
     */
    public function getDamage()
    {
        return $this->Damage;
    }

}

class EntityMapper
{
    protected static $connection;

    function __construct()
    {
        $user = 'back';
        $password = '12345';
        $db = 'data_mapper';
        $host = 'localhost';
        try {
            self::$connection = new PDO("mysql:host=$host;dbname=$db", $user, $password);
        } catch (PDOException $e) {
            echo " не соединено с БД";
            print "Error!: " . $e->getMessage();
            die();
        }
    }

    public function Save(Entity $Entity)
    {
        $insertQuery = 'Insert Into ENTITIES Values (?,?,?,?)';
        $query = self::$connection->prepare($insertQuery);
        $query->execute([$Entity->ID, $Entity->Parametr, $Entity->Health, $Entity->Damage]);
    }

    public function Remove(Entity $Entity): bool
    {
        $Statement = self::$connection->prepare("Delete From ENTITIES Where ID = ?, Parametr = ?, Health = ?, Damage = ?)");
        $Result = $Statement->execute(array($Entity->ID, $Entity->Parametr, $Entity->Health, $Entity->Damage));
        return $Result;
    }

    public function GetById($ID): Entity
    {
        $Statement = self::$connection->prepare("Select * From ENTITIES Where ID = ?");
        $Entity = $Statement->execute(array($ID));
        $Result = new Entity;
        if (!empty($Entity)) {
            $Result->ID = $Entity["ID"];
            $Result->Parametr = $Entity["Parametr"];
            $Result->Health = $Entity["Health"];
            $Result->Damage = $Entity["Damage"];
        }
        return $Result;
    }

    public function GetAll(): array
    {
        $Result = array();
        foreach (self::$connection->query('Select * From ENTITIES') as $Entity) {
            $ToBePushed = new Entity;
            $ToBePushed->ID = $Entity["ID"];
            $ToBePushed->Parametr = $Entity["Parametr"];
            $ToBePushed->Health = $Entity["Health"];
            $ToBePushed->Damage = $Entity["Damage"];
            array_push($Result, $ToBePushed);
        }
        return $Result;
    }

    public function GetByDamage($Damage): array
    {
        $Result = array();
        $Statement = self::$connection->prepare("Select * From ENTITIES Where Damage = ?");
        foreach ($Statement->execute(array($Damage)) as $Entity) {
            $ToBePushed = new Entity;
            $ToBePushed->ID = $Entity["ID"];
            $ToBePushed->Parametr = $Entity["Parametr"];
            $ToBePushed->Health = $Entity["Health"];
            $ToBePushed->Damage = $Entity["Damage"];
            array_push($Result, $ToBePushed);
        }
        return $Result;
    }
}

class EntityRepository
{
    public static function store(Entity $entity)
    {
        return EntityMapper::Save($entity);
    }

    public static function Remove(Entity $entity)
    {
        EntityMapper::Remove($entity);
    }

    public static function getAll(): array
    {
        return EntityMapper::getAll();
    }

    public static function getById($id): Entity
    {
        return EntityMapper::getById($id);
    }

    public static function GetByDamage($Damage): array
    {
        return EntityMapper::GetByDamage($Damage);
    }
}

// пример
$Mapper = new EntityMapper();
$Entity = new Entity();
$Entity->ID = 2;
$Entity->Parametr = 17;
$Entity->Health = 2000;
$Entity->Damage = 64;

$Mapper->Save($Entity);
$EntityArray = $Mapper->GetAll();
var_dump($EntityArray);