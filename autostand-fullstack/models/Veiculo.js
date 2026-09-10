// models/Veiculo.js
// Classe base "abstrata". JavaScript nao possui abstract class nativa,
// por isso simulamos esse comportamento impedindo new Veiculo().

class Veiculo {
    constructor({ id = null, marca, modelo, ano, preco, combustivel, quilometragem = 0, estado = 'disponivel', createdAt = null }) {
        if (new.target === Veiculo) {
            throw new Error('Veiculo e uma classe abstrata e nao deve ser instanciada diretamente.');
        }

        this.id = id;
        this.marca = marca;
        this.modelo = modelo;
        this.ano = Number(ano);
        this.preco = Number(preco);
        this.combustivel = combustivel;
        this.quilometragem = Number(quilometragem);
        this.estado = estado;
        this.createdAt = createdAt;
    }

    getNomeCompleto() {
        return `${this.marca} ${this.modelo}`;
    }

    getTipo() {
        throw new Error('A classe filha tem de implementar getTipo().');
    }

    getInfoEspecifica() {
        throw new Error('A classe filha tem de implementar getInfoEspecifica().');
    }

    toJSON() {
        return {
            id: this.id,
            tipo: this.getTipo(),
            marca: this.marca,
            modelo: this.modelo,
            nomeCompleto: this.getNomeCompleto(),
            ano: this.ano,
            preco: this.preco,
            combustivel: this.combustivel,
            quilometragem: this.quilometragem,
            estado: this.estado,
            infoEspecifica: this.getInfoEspecifica(),
            created_at: this.createdAt
        };
    }
}

module.exports = Veiculo;
