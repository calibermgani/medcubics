<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App;
// /use Nwidart\DbExporter\DbMigrations;

class Dbmigrations extends \Nwidart\DbExporter\DbMigrations {

    function __construct($database)
    {
        if (empty($database)) {
            throw new InvalidDatabaseException('No database set in app/config/database.php');
        }
        $database = str_replace('_', '',$database);
        $this->database = $database;

    }

    public function write($type = null)
    {
        // Check if convert method was called before
        // If not, call it on default DB
        if($type){
             $schema = $this->newcompile();
        } else {
             if (!$this->customDb) {
                $this->convert();
            }

            $schema = $this->compile();
            $filename = date('Y_m_d_His') . "_create_" . $this->database . "_database.php";

            self::$filePath = $this->getpath()."{$filename}";
            file_put_contents(self::$filePath, $schema);

            return self::$filePath;
        }       
    }

    public function convert($database = null)
    {
        if (!is_null($database)) {
            $this->database = $database;
            $this->customDb = true;
        }

        $tables = $this->getTables();
       
        // Loop over the tables
        foreach ($tables as $key => $value) {
            // Do not export the ignored tables
            if (in_array($value['table_name'], self::$ignore)) {
                continue;
            }

            $down = "Schema::drop('{$value['table_name']}');";
            $up = "Schema::create('{$value['table_name']}', function($" . "table) {\n";

            $tableDescribes = $this->getTableDescribes($value['table_name']);
            // Loop over the tables fields
            foreach ($tableDescribes as $values) {
                $method = "";
                $para = strpos($values->Type, '(');
                $type = $para > -1 ? substr($values->Type, 0, $para) : $values->Type;
                $numbers = "";
                $nullable = $values->Null == "NO" ? "" : "->nullable()";
                $default = empty($values->Default) ? "" : "->default('{$values->Default}')";
                $unsigned = strpos($values->Type, "unsigned") === false ? '' : '->unsigned()';

                switch ($type) {
                    case 'int' :
                        $method = 'integer';
                        break;
                    case 'smallint' :
                        $method = 'smallInteger';
                        break;
                    case 'bigint' :
                        $method = 'bigInteger';
                        break;
                    case 'char' :
                    case 'varchar' :
                        $para = strpos($values->Type, '(');
                        $numbers = ", " . substr($values->Type, $para + 1, -1);
                        $method = 'string';
                        break;
                    case 'float' :
                        $method = 'float';
                        break;
                    case 'double' :
                        $para = strpos($values->Type, '('); # 6
                        $numbers = ", " . substr($values->Type, $para + 1, -1);
                        $method = 'double';
                        break;
                    case 'decimal' :
                        $para = strpos($values->Type, '(');
                        $numbers = ", " . substr($values->Type, $para + 1, -1);
                        $method = 'decimal';
                        break;
                    case 'tinyint' :
                        $method = 'boolean';
                        break;
                    case 'date' :
                        $method = 'date';
                        break;
                    case 'timestamp' :
                        $method = 'timestamp';
                        break;
                    case 'datetime' :
                        $method = 'dateTime';
                        break;
                    case 'longtext' :
                        $method = 'longText';
                        break;
                    case 'mediumtext' :
                        $method = 'mediumText';
                        break;
                    case 'text' :
                        $method = 'text';
                        break;
                    case 'time' :
                        $method = 'time';
                        break;
                    case 'longblob':
                    case 'blob' :
                        $method = 'binary';
                        break;
                    case 'enum' :
                        $method = 'enum';
                        $para = strpos($values->Type, '('); # 4
                        $options = substr($values->Type, $para + 1, -1);
                        $numbers = ', array(' . $options . ')';
                        break;
                }                
                if ($values->Key == 'PRI') {
                    $method = 'increments';
                }       
                if ($values->Key == 'PRI' && $type == "bigint") {
                    $method = 'bigIncrements';
                }

                $up .= "                $" . "table->{$method}('{$values->Field}'{$numbers}){$nullable}{$default}{$unsigned};\n";
            }

            $tableIndexes = $this->getTableIndexes($value['table_name']);
            if (!is_null($tableIndexes) && count((array)$tableIndexes)){
                foreach ($tableIndexes as $index) {
                    $up .= '                $' . "table->index('" . $index['Key_name'] . "');\n";
                }
            }

            $up .= "            });\n\n";

            $this->schema[$value['table_name']] = array(
                'up'   => $up,
                'down' => $down
            );
        }

        return $this;
    }

    public function getpath()
    { 
        $path = base_path().'/database/migrations/practicemigration/';
        if(!file_exists($path)) {
            mkdir($path);
        }
        return $path;
    }
}