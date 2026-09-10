// O campo password fica encapsulado num campo privado (#passwordHash).

class User {
    #passwordHash;

    constructor({ id = null, name, email, passwordHash = null, role = 'viewer', createdAt = null }) {
        this.id = id;
        this.name = name;
        this.email = email;
        this.role = role;
        this.createdAt = createdAt;
        this.#passwordHash = passwordHash;
    }

    getPasswordHash() {
        return this.#passwordHash;
    }

    toJSON() {
        // Nunca devolvemos a password/hash ao front-end.
        return {
            id: this.id,
            name: this.name,
            email: this.email,
            role: this.role,
            created_at: this.createdAt
        };
    }
}

module.exports = User;
