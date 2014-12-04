<?php

class User
{
    /**
     * Aura SQL ExtendedPdo instance (PDO)
     * @var \Aura\Sql\ExtendedPdo
     */
    private $pdo;

    /**
     * Init the object and set the PDO instance
     *
     * @param \Aura\Sql\ExtendedPdo $pdo Aura SQL instance
     */
    public function __construct(\Aura\Sql\ExtendedPdo $pdo)
    {
        $this->setPdo($pdo);
    }

    /**
     * Set PDO instance
     *
     * @param \Aura\Sql\ExtendedPdo $pdo Aura SQL instance
     */
    public function setPdo(\Aura\Sql\ExtendedPdo $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get the current PDO instance
     *
     * @return \Aura\Sql\ExtendedPdo instance
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    public function add($data)
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $sql = 'insert into users (username, password, email_address) values (:username, :password, :email)';
        $result = $this->getPdo()->perform($sql, $data);
        return true;
    }
}