// Cart Management System
// This script handles cart operations across all pages using localStorage

class CartManager {
    constructor() {
        this.cartKey = 'cafe_cart_items';
        this.cartItems = this.loadCartFromStorage();
    }

    // Load cart items from localStorage
    loadCartFromStorage() {
        try {
            const stored = localStorage.getItem(this.cartKey);
            return stored ? JSON.parse(stored) : [];
        } catch (error) {
            console.error('Error loading cart from storage:', error);
            return [];
        }
    }

    // Save cart items to localStorage
    saveCartToStorage() {
        try {
            localStorage.setItem(this.cartKey, JSON.stringify(this.cartItems));
        } catch (error) {
            console.error('Error saving cart to storage:', error);
        }
    }

    // Add item to cart
    addItem(name, price, quantity = 1) {
        // Extract numeric price
        const priceValue = parseFloat(price.replace(/[^\d.]/g, ''));
        
        // Check if item already exists
        const existingItem = this.cartItems.find(item => item.name === name);
        
        if (existingItem) {
            // Update existing item
            existingItem.quantity += quantity;
            existingItem.total = existingItem.quantity * priceValue;
        } else {
            // Add new item
            const newItem = {
                name: name,
                price: priceValue,
                quantity: quantity,
                total: priceValue * quantity
            };
            this.cartItems.push(newItem);
        }
        
        this.saveCartToStorage();
        this.updateCartDisplay();
        return this.cartItems;
    }

    // Update item quantity
    updateQuantity(name, newQuantity) {
        const item = this.cartItems.find(item => item.name === name);
        if (item) {
            if (newQuantity <= 0) {
                this.removeItem(name);
            } else {
                item.quantity = newQuantity;
                item.total = item.price * newQuantity;
                this.saveCartToStorage();
                this.updateCartDisplay();
            }
        }
    }

    // Remove item from cart
    removeItem(name) {
        this.cartItems = this.cartItems.filter(item => item.name !== name);
        this.saveCartToStorage();
        this.updateCartDisplay();
    }

    // Get all cart items
    getCartItems() {
        return this.cartItems;
    }

    // Get cart total
    getCartTotal() {
        return this.cartItems.reduce((total, item) => total + item.total, 0);
    }

    // Get cart count (total number of items)
    getCartCount() {
        return this.cartItems.reduce((count, item) => count + item.quantity, 0);
    }

    // Clear entire cart
    clearCart() {
        this.cartItems = [];
        this.saveCartToStorage();
        this.updateCartDisplay();
    }

    // Update cart display in navigation (if cart icon exists)
    updateCartDisplay() {
        const cartIcon = document.querySelector('.cart i');
        const cartCount = this.getCartCount();
        
        if (cartIcon) {
            // Add cart count badge if items exist
            let badge = cartIcon.parentElement.querySelector('.cart-badge');
            if (cartCount > 0) {
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'cart-badge';
                    badge.style.cssText = `
                        position: absolute;
                        top: -8px;
                        right: -8px;
                        background: #e74c3c;
                        color: white;
                        border-radius: 50%;
                        width: 20px;
                        height: 20px;
                        font-size: 12px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-weight: bold;
                    `;
                    cartIcon.parentElement.style.position = 'relative';
                    cartIcon.parentElement.appendChild(badge);
                }
                badge.textContent = cartCount;
            } else if (badge) {
                badge.remove();
            }
        }
    }

    // Show toast notification
    showToast(message, type = 'success') {
        // Remove existing toast
        const existingToast = document.querySelector('.cart-toast');
        if (existingToast) {
            existingToast.remove();
        }

        const toast = document.createElement('div');
        toast.className = `cart-toast cart-toast-${type}`;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#27ae60' : '#e74c3c'};
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 10000;
            font-size: 14px;
            font-weight: 500;
            animation: slideInRight 0.3s ease;
        `;
        
        toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span style="margin-left: 8px;">${message}</span>
        `;

        // Add animation styles
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOutRight {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);

        document.body.appendChild(toast);

        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 300);
        }, 3000);
    }
}

// Create global cart manager instance
window.cartManager = new CartManager();
