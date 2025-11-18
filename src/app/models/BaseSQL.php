<?php

namespace app\models;

use PDO;
use PDOException;
use Exception;

class BaseSQL {
    protected $db;
    protected $table;
    protected $primaryKey;

    public function __construct(PDO $db, string $table, string $primaryKey) {
        $this->db = $db;
        $this->table = $table;
        $this->primaryKey = $primaryKey;
    }

    public function getDb() {
        return $this->db;
    }

    public function getTable() {
        return $this->table;
    }

    public function getPrimaryKey() {
        return $this->primaryKey;
    }

    public function insert(array $data): bool {
        try {
            $columns = "`" . implode("`, `", array_keys($data)) . "`";
            $placeholders = ":" . implode(", :", array_keys($data));
            $sql = "INSERT INTO `{$this->table}` ($columns) VALUES ($placeholders)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            print($e);
            return false; 
        }
    }

    public function update($id, array $data): bool {
        try {
            $setPart = implode(", ", array_map(fn($key) => "`$key` = :$key", array_keys($data)));
            $sql = "UPDATE `{$this->table}` SET $setPart WHERE `{$this->primaryKey}` = :id";
            $stmt = $this->db->prepare($sql);
            $data['id'] = $id;
            return $stmt->execute($data);
        } catch (PDOException $e) {
            print($e);
            return false;
        }
    }

    public function delete($id): bool {
        try {
            $sql = "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getAll(): array {
        try {
            $sql = "SELECT * FROM `{$this->table}`";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getById($id): ?array {
        try {
            $sql = "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getWhere(array $conditions): array {
        try {
            $where = implode(" AND ", array_map(fn($key) => "`$key` = :$key", array_keys($conditions)));
            $sql = "SELECT * FROM `{$this->table}` WHERE $where";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($conditions);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getTableCol() {
        try {
            $sql = "SHOW columns FROM `{$this->table}`";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }   
    }

    public function getForeignKeys(): array {
        $dbName = $this->db->query("SELECT DATABASE()")->fetchColumn();
    
        $query = "SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
                  FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                  WHERE TABLE_NAME = :table
                  AND TABLE_SCHEMA = :db
                  AND REFERENCED_TABLE_NAME IS NOT NULL";
    
        $stmt = $this->db->prepare($query);
        $stmt->execute(['table' => $this->table, 'db' => $dbName]);
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchForeignKeyData(array $foreignKeys): array {
        $foreignKeyData = [];
    
        foreach ($foreignKeys as $fk) {
            $query = "SELECT {$fk['column']}, {$fk['display']} FROM {$fk['table']}";
            $stmt = $this->db->query($query);
            $foreignKeyData[$fk['column']] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        }
    
        return $foreignKeyData;
    }
    
    public function insertFromCSV(string $filePath): array {
        $results = ['inserted' => 0, 'errors' => []];
    
        if (!file_exists($filePath) || !is_readable($filePath)) {
            $results['errors'][] = "Fichier introuvable ou illisible.";
            print_r($results);
            return $results;
        }
    
        try {
            $handle = fopen($filePath, "r");
            if ($handle === false) {
                $results['errors'][] = "Impossible d'ouvrir le fichier.";
                print_r($results);
                return $results;
            }
    
            $header = fgetcsv($handle, 0, ";");
            if (!$header || !is_array($header)) {
                $results['errors'][] = "Fichier CSV vide ou mal formaté.";
                fclose($handle);
                print_r($results);
                return $results;
            }
    
            $header = array_map(fn($col) => is_string($col) ? trim(trim($col), '"') : '', $header);
    
            while (($row = fgetcsv($handle, 0, ";")) !== false) {
                if (!is_array($row)) continue; 
    
                $row = array_map(fn($val) => is_string($val) ? trim(trim($val), '"') : '', $row);
    
                if (count($row) !== count($header)) {
                    $results['errors'][] = "Ligne invalide : " . implode(";", $row);
                    continue;
                }
    
                $data = array_combine($header, $row);
                print_r($data);
                if ($this->insert($data)) {
                    $results['inserted']++;
                } else {
                    $results['errors'][] = "Échec insertion : " . implode(";", $row);
                }
            }
    
            fclose($handle);
        } catch (Exception $e) {
            print($e);
            $results['errors'][] = "Erreur : " . $e->getMessage();
        }
    
        return $results;
    }
    
    

    // /**
    //  * Insère les données d'un fichier CSV dans la table en prenant en compte les types des colonnes.
    //  * 
    //  * @param string $csvFile Le chemin vers le fichier CSV.
    //  * @param string $separator Le séparateur utilisé dans le CSV (par exemple, ',' ou ';').
    //  * @return bool Retourne true si l'insertion a réussi, sinon false.
    //  */
    // public function insertCSV(string $csvFile, string $separator = ','): bool {
    //     // Vérifier si le fichier existe
    //     if (!file_exists($csvFile) || !is_readable($csvFile)) {
    //         throw new Exception("Fichier CSV invalide ou introuvable.");
    //     }

    //     // Ouvrir le fichier CSV
    //     if (($handle = fopen($csvFile, 'r')) === false) {
    //         throw new Exception("Impossible d'ouvrir le fichier CSV.");
    //     }

    //     // Lire la première ligne pour obtenir les noms des colonnes
    //     $columns = fgetcsv($handle, 0, $separator);

    //     // Supprimer la clé primaire des colonnes, car elle sera générée automatiquement
    //     $columns = array_filter($columns, function ($col) {
    //         return $col != $this->primaryKey;
    //     });
    //     $columns = array_values($columns); // Réindexer les clés du tableau

    //     // Récupérer les types des colonnes depuis la base de données
    //     $columnTypes = $this->getColumnTypes();

    //     // Préparer la requête d'insertion
    //     $columnList = implode(", ", $columns);
    //     $placeholders = implode(", ", array_fill(0, count($columns), "?"));
    //     $sql = "INSERT INTO {$this->table} (" . $columnList . ") VALUES (" . $placeholders . ")";
    //     $stmt = $this->db->prepare($sql);

    //     // Insérer les données ligne par ligne
    //     $this->db->beginTransaction();
    //     try {
    //         while (($row = fgetcsv($handle, 0, $separator)) !== false) {
    //             // Filtrer les colonnes pour ne pas inclure la clé primaire
    //             $row = array_filter($row, function ($key) use ($columns) {
    //                 return in_array($key, $columns);
    //             }, ARRAY_FILTER_USE_KEY);
    //             $row = array_values($row); // Réindexer les clés du tableau

    //             // Convertir les valeurs en fonction des types des colonnes
    //             $convertedRow = [];
    //             foreach ($row as $key => $value) {
    //                 $columnName = $columns[$key];
    //                 $type = $columnTypes[$columnName] ?? 'string';

    //                 // Convertir les données en fonction du type de la colonne
    //                 switch ($type) {
    //                     case 'int':
    //                         $convertedRow[] = (int) $value;
    //                         break;
    //                     case 'decimal':
    //                         $convertedRow[] = (float) $value;
    //                         break;
    //                     case 'date':
    //                         $convertedRow[] = date('Y-m-d', strtotime($value)); // Conversion en format date
    //                         break;
    //                     default:
    //                         $convertedRow[] = (string) $value; // Par défaut, convertir en chaîne
    //                         break;
    //                 }
    //             }

    //             // Exécuter l'insertion
    //             $stmt->execute($convertedRow);
    //         }
            
    //         // Commit les changements après toutes les insertions
    //         $this->db->commit();
    //         fclose($handle);
    //         return true;
    //     } catch (Exception $e) {
    //         // Si une erreur survient, rollback
    //         $this->db->rollBack();
    //         fclose($handle);
    //         throw new Exception("Erreur lors de l'insertion des données : " . $e->getMessage());
    //     }
    // }

    // /**
    //  * Récupère les types des colonnes de la table.
    //  *
    //  * @return array Un tableau associatif avec le nom de la colonne comme clé et son type comme valeur.
    //  */
    // private function getColumnTypes(): array {
    //     $query = "DESCRIBE {$this->table}";
    //     $stmt = $this->db->query($query);
    //     $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //     $columnTypes = [];
    //     foreach ($columns as $column) {
    //         $columnTypes[$column['Field']] = $this->getColumnType($column['Type']);
    //     }

    //     return $columnTypes;
    // }

    // /**
    //  * Retourne le type de la colonne en fonction du type MySQL.
    //  *
    //  * @param string $mysqlType Le type MySQL de la colonne (par exemple, 'int', 'varchar(255)', 'date').
    //  * @return string Le type PHP correspondant.
    //  */
    // private function getColumnType(string $mysqlType): string {
    //     if (stripos($mysqlType, 'int') !== false) {
    //         return 'int';
    //     } elseif (stripos($mysqlType, 'decimal') !== false || stripos($mysqlType, 'float') !== false) {
    //         return 'decimal';
    //     } elseif (stripos($mysqlType, 'date') !== false || stripos($mysqlType, 'datetime') !== false) {
    //         return 'date';
    //     } else {
    //         return 'string';
    //     }
    // }
}
    
?>
