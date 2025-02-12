<?php
class Product {
    private $conn;
    private $table_name = "products";

    // Product properties
    public $id;
    public $name;
    public $price;
    public $stock;
    public $description;
    public $sku;
    public $category;
    public $weight;
    public $dimensions;
    public $image;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new product
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, price=:price, stock=:stock, description=:description, 
                      sku=:sku, category=:category, weight=:weight, dimensions=:dimensions, image=:image";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->stock = htmlspecialchars(strip_tags($this->stock));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->sku = htmlspecialchars(strip_tags($this->sku));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->weight = htmlspecialchars(strip_tags($this->weight));
        $this->dimensions = htmlspecialchars(strip_tags($this->dimensions));
        $this->image = htmlspecialchars(strip_tags($this->image));

        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":stock", $this->stock);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":sku", $this->sku);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":weight", $this->weight);
        $stmt->bindParam(":dimensions", $this->dimensions);
        $stmt->bindParam(":image", $this->image);

        // Execute query
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Read all products
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read single product
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->name = $row['name'];
        $this->price = $row['price'];
        $this->stock = $row['stock'];
        $this->description = $row['description'];
        $this->sku = $row['sku'];
        $this->category = $row['category'];
        $this->weight = $row['weight'];
        $this->dimensions = $row['dimensions'];
        $this->image = $row['image'];
    }

    // Update product
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET name=:name, price=:price, stock=:stock, description=:description,
                      sku=:sku, category=:category, weight=:weight, dimensions=:dimensions, image=:image
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->stock = htmlspecialchars(strip_tags($this->stock));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->sku = htmlspecialchars(strip_tags($this->sku));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->weight = htmlspecialchars(strip_tags($this->weight));
        $this->dimensions = htmlspecialchars(strip_tags($this->dimensions));
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind parameters
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':stock', $this->stock);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':sku', $this->sku);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':weight', $this->weight);
        $stmt->bindParam(':dimensions', $this->dimensions);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':id', $this->id);

        // Execute query
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete product
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Search products
    public function search($keywords) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE name LIKE ? OR description LIKE ? OR sku LIKE ?";
        $stmt = $this->conn->prepare($query);

        $keywords = htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";

        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);

        $stmt->execute();
        return $stmt;
    }
}
?>