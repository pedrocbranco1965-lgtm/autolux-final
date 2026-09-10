const Veiculo = require('./Veiculo');

class Carro extends Veiculo {
    constructor(dados) {
        super(dados);
        this.portas = Number(dados.portas);
    }

    getTipo() {
        return 'carro';
    }

    getInfoEspecifica() {
        return `${this.portas} portas`;
    }

    toJSON() {
        return {
            ...super.toJSON(),
            portas: this.portas,
            cilindradas: null
        };
    }
}

module.exports = Carro;
