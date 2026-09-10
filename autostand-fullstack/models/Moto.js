const Veiculo = require('./Veiculo');

class Moto extends Veiculo {
    constructor(dados) {
        super(dados);
        this.cilindradas = Number(dados.cilindradas);
    }

    getTipo() {
        return 'moto';
    }

    getInfoEspecifica() {
        return `${this.cilindradas} cc`;
    }

    toJSON() {
        return {
            ...super.toJSON(),
            portas: null,
            cilindradas: this.cilindradas
        };
    }
}

module.exports = Moto;
