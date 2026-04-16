<?php

/**
 * Shopping Cart Class
 * Handles all shopping cart operations using cookies
 */

class ShoppingCart
{
    private const COOKIE_NAME = 'shopping_cart';

    /**
     * Get cart data from cookie
     * 
     * @return array Cart items
     */
    public function get_cart_items()
    {
        if (!isset($_COOKIE[self::COOKIE_NAME])) {
            return [];
        }

        $cookie_data = stripslashes($_COOKIE[self::COOKIE_NAME]);
        return json_decode($cookie_data, true) ?? [];
    }

    /**
     * Save cart data to cookie
     * 
     * @param array $cart_data Cart items
     * @return bool Success status
     */
    private function save_cart($cart_data)
    {
        $item_data = json_encode($cart_data);
        return setcookie(
            self::COOKIE_NAME,
            $item_data,
            time() + COOKIE_EXPIRY_SECONDS,
            '/'
        );
    }

    /**
     * Add item to cart
     * 
     * @param int $item_id Product ID
     * @param string $item_name Product name
     * @param float $item_price Product price
     * @param int $quantity Quantity to add
     * @return bool Success status
     */
    public function add_item($item_id, $item_name, $item_price, $quantity)
    {
        $cart_data = $this->get_cart_items();

        // Check if item already exists in cart
        $item_exists = false;
        foreach ($cart_data as $key => $item) {
            if ($item['item_id'] == $item_id) {
                $cart_data[$key]['item_quantity'] += $quantity;
                $item_exists = true;
                break;
            }
        }

        // Add new item if it doesn't exist
        if (!$item_exists) {
            $cart_data[] = [
                'item_id' => $item_id,
                'item_name' => $item_name,
                'item_price' => $item_price,
                'item_quantity' => $quantity,
            ];
        }

        return $this->save_cart($cart_data);
    }

    /**
     * Remove item from cart
     * 
     * @param int $item_id Product ID to remove
     * @return bool Success status
     */
    public function remove_item($item_id)
    {
        $cart_data = $this->get_cart_items();

        foreach ($cart_data as $key => $item) {
            if ($item['item_id'] == $item_id) {
                unset($cart_data[$key]);
                return $this->save_cart(array_values($cart_data));
            }
        }

        return false;
    }

    /**
     * Clear all items from cart
     * 
     * @return bool Success status
     */
    public function clear_cart()
    {
        return setcookie(self::COOKIE_NAME, "", time() - 3600, '/');
    }

    /**
     * Calculate total cart value
     * 
     * @return float Total price
     */
    public function get_cart_total()
    {
        $cart_data = $this->get_cart_items();
        $total = 0;

        foreach ($cart_data as $item) {
            $total += $item['item_quantity'] * $item['item_price'];
        }

        return $total;
    }

    /**
     * Get cart item count
     * 
     * @return int Number of items in cart
     */
    public function get_item_count()
    {
        return count($this->get_cart_items());
    }
}
