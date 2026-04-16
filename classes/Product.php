<?php

/**
 * Product Class
 * Handles all product-related database operations
 */

class Product
{
    private $db_connection;

    /**
     * Constructor
     * 
     * @param PDO $connection Database connection
     */
    public function __construct($connection)
    {
        $this->db_connection = $connection;
    }

    /**
     * Get all products
     * 
     * @return array List of all products
     */
    public function get_all_products()
    {
        try {
            $query = "SELECT * FROM tbl_product ORDER BY id ASC";
            $statement = $this->db_connection->prepare($query);
            $statement->execute();
            return $statement->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching products: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get product by ID
     * 
     * @param int $product_id Product ID
     * @return array|null Product data or null if not found
     */
    public function get_product_by_id($product_id)
    {
        try {
            $query = "SELECT * FROM tbl_product WHERE id = :id";
            $statement = $this->db_connection->prepare($query);
            $statement->bindParam(':id', $product_id, PDO::PARAM_INT);
            $statement->execute();

            $result = $statement->fetch();
            return $result ? $result : null;
        } catch (PDOException $e) {
            error_log("Error fetching product: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Add new product
     * 
     * @param string $name Product name
     * @param float $price Product price
     * @param string $image_filename Image filename
     * @param int $quantity Product quantity
     * @return bool Success status
     */
    public function add_product($name, $price, $image_filename, $quantity)
    {
        try {
            $query = "INSERT INTO tbl_product (name, price, image, quantity) 
                      VALUES (:name, :price, :image, :quantity)";

            $statement = $this->db_connection->prepare($query);
            $statement->bindParam(':name', $name);
            $statement->bindParam(':price', $price);
            $statement->bindParam(':image', $image_filename);
            $statement->bindParam(':quantity', $quantity, PDO::PARAM_INT);

            return $statement->execute();
        } catch (PDOException $e) {
            error_log("Error adding product: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update existing product
     * 
     * @param int $product_id Product ID
     * @param string $name Product name
     * @param float $price Product price
     * @param string $image_filename Image filename
     * @param int $quantity Product quantity
     * @return bool Success status
     */
    public function update_product($product_id, $name, $price, $image_filename, $quantity)
    {
        try {
            $query = "UPDATE tbl_product 
                      SET name = :name, quantity = :quantity, price = :price, image = :image 
                      WHERE id = :id";

            $statement = $this->db_connection->prepare($query);
            $statement->bindParam(':name', $name);
            $statement->bindParam(':price', $price);
            $statement->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $statement->bindParam(':image', $image_filename);
            $statement->bindParam(':id', $product_id, PDO::PARAM_INT);

            return $statement->execute();
        } catch (PDOException $e) {
            error_log("Error updating product: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete product
     * 
     * @param int $product_id Product ID
     * @return bool Success status
     */
    public function delete_product($product_id)
    {
        try {
            $query = "DELETE FROM tbl_product WHERE id = :id";
            $statement = $this->db_connection->prepare($query);
            $statement->bindParam(':id', $product_id, PDO::PARAM_INT);

            return $statement->execute();
        } catch (PDOException $e) {
            error_log("Error deleting product: " . $e->getMessage());
            return false;
        }
    }
}
