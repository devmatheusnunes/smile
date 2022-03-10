<?php

namespace Core\Database;

class QueryBuilder extends Connection
{
    private $clausules = [];

    /**
     * undocumented function
     *
     * @return void
     */
    public function __call(string $name, array $arguments)
    {
        $clausule = $arguments [0];
        if (count($arguments) > 1) {
           $clausule = $arguments; 
        }
        $this->clausules[strtolower($name)] = $clausule;

        return $this;
    }

    /**
     * @param 
     */
    public function __construct($options)
    {
        parent::__construct($options); //coisa do PDO
    }

    public function insert($values)
    {
        // recupera o nome da tabela
        // ou deixa uma marcação usada para lançar uma excessão
        $table = isset($this->clausules['table']) && is_string($this->clausules['table'])? $this->clausules['table'] : '<table>';
        if ($table == '<table>') {
            throw new Exception('A string was expected');
        }

        // recupera o array dos campos
        // ou deixa uma marcação usada para lançar uma excessão
        $_fields = isset($this->clausules['fields'])
            && is_array($this->clausules['fields'])
            ? $this->clausules['fields'] : '<field>';
        if ($_fields == '<field>') {
            throw new Exception('A array was expected');
        }
        // cria uma string a partir do array
        $fields = implode(', ', $_fields);
        
        // cria uma lista de rótulos para usar "prepared statement"
        $_placeholders = array_map(function() {
            return '?';
        }, $_fields);
        // cria uma string a partir do array
        $placeholders = implode(', ', $_placeholders);
        
        // monta array com os componentes da instrução
        $command = [];
        $command[] = 'INSERT INTO';
        $command[] = $table;
        $command[] = '(' . $fields . ')';
        $command[] = 'VALUES';
        $command[] = '(' . $placeholders . ')';
        
        // junta o comando, a estrutura fica assim:
        // INSERT INTO {table} ({fields}) VALUES ({values});
		$sql = implode(' ', $command);

        return $this->executeInsert($sql, $values);
    }

    public function select(array $values = [])
    {
        
        // recupera o nome da tabela
        // ou deixa uma marcação usada para lançar uma excessão
        $table = isset($this->clausules['table']) && is_string($this->clausules['table'])? $this->clausules['table'] : '<table>';
        if ($table == '<table>') {
            throw new Exception('A string was expected');
        }

        // recupera o array dos campos
        // ou deixa uma marcação usada para lançar uma excessão
        $_fields = isset($this->clausules['fields'])
            && is_array($this->clausules['fields'])
            ? $this->clausules['fields'] : '<field>';
        if ($_fields == '<field>') {
            throw new Exception('A array was expected');
        }
        // cria uma string a partir do array
        $fields = implode(', ', $_fields);

        $join = isset($this->clausules['join']) ? $this->clausules['join'] : '';

        // monta array com os componentes da instrução
        $command = [];
        $command[] = 'SELECT';
        $command[] = $fields;
        $command[] = 'FROM';
        $command[] = $table;

        if ($join) {
            $command[] = $join;
        }

        $clausules = [
            'where' => [
                'instruction' => 'WHERE',
                'separator' => ' ',
            ],
            'group' => [
                'instruction' => 'GROUP BY',
                'separator' => ', ',
            ],
            'order' => [
                'instruction' => 'ORDER BY',
                'separator' => ', ',
            ],
            'having' => [
                'instruction' => 'HAVING',
                'separator' => ' AND ',
            ],
            'limit' => [
                'instruction' => 'LIMIT',
                'separator' => ',',
            ],
        ];

        foreach($clausules as $key => $clausule) {
            if (isset($this->clausules[$key])) {
                $value = $this->clausules[$key];
                if (is_array($value)) {
                    $value = implode($clausule['separator'], $this->clausules[$key]);
                }
                $command[] = $clausule['instruction'] . ' ' . $value;
            }
        }
        // SELECT {fields} FROM <JOIN> {table} <WHERE> <GROUP> <ORDER> <HAVING> <LIMIT>;
        // junta o comando
        $sql = implode(' ', $command);

        return $this->executeSelect($sql, $values);
    }

    public function update($values, $filters = [])
    {
        // recupera o nome da tabela
        // ou deixa uma marcação usada para lançar uma excessão
        $table = isset($this->clausules['table']) && is_string($this->clausules['table'])? $this->clausules['table'] : '<table>';
        if ($table == '<table>') {
            throw new Exception('A string was expected');
        }

        $join = isset($this->clausules['join']) ? $this->clausules['join'] : '';

        // recupera o array dos campos
        // ou deixa uma marcação usada para lançar uma excessão
        $_fields = isset($this->clausules['fields'])
            && is_array($this->clausules['fields'])
            ? $this->clausules['fields'] : '<field>';
        if ($_fields == '<field>') {
            throw new Exception('A array was expected');
        }
        
        //cria uma string com os campos e os
        //rótulos para usar "prepared statement"
        $sets = $_fields;
        if (is_array($_fields)) {
            $sets = implode(', ', array_map(function($value) {
                return $value . ' = ?';
            }, $_fields));
        }

        // monta array com os componentes da instrução
        $command = [];
        $command[] = 'UPDATE';
        $command[] = $table;
        if ($join) {
            $command[] = $join;
        }
        $command[] = 'SET';
        $command[] = $sets;
        $clausules = [
            'where' => [
                'instruction' => 'WHERE',
                'separator' => ' ',
            ]
        ];        

        //monta a clausula 'where' no array
        foreach($clausules as $key => $clausule) {
            if (isset($this->clausules[$key])) {
                $value = $this->clausules[$key];
                if (is_array($value)) {
                    $value = implode($clausule['separator'], $this->clausules[$key]);
                }
                $command[] = $clausule['instruction'] . ' ' . $value;
            }
        }

        // UPDATE {table} SET {set} <WHERE>
        // junta o comando
        $sql = implode(' ', $command);

        return $this->executeUpdate($sql, array_merge($values, $filters));
    }

    public function delete($filters)
    {
        // recupera o nome da tabela
        // ou deixa uma marcação usada para lançar uma excessão
        $table = isset($this->clausules['table']) && is_string($this->clausules['table'])? $this->clausules['table'] : '<table>';
        if ($table == '<table>') {
            throw new Exception('A string was expected');
        }

        $join = isset($this->clausules['join']) ? $this->clausules['join'] : '';

        // monta array com os componentes da instrução
        $command = [];
        $command[] = 'DELETE FROM';
        $command[] = $table;
        if ($join) {
            $command[] = $join;
        }
        $clausules = [
            'where' => [
                'instruction' => 'WHERE',
                'separator' => ' ',
            ]
        ];

        //monta a clausula 'where' no array
        foreach($clausules as $key => $clausule) {
            if (isset($this->clausules[$key])) {
                $value = $this->clausules[$key];
                if (is_array($value)) {
                    $value = implode($clausule['separator'], $this->clausules[$key]);
                }
                $command[] = $clausule['instruction'] . ' ' . $value;
            }
        }

        // DELETE FROM {table} <JOIN> <USING> <WHERE>
        // junta o comando
        $sql = implode(' ', $command);

        return $this->executeDelete($sql, $filters);
    }
}

