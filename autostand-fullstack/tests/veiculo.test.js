const Veiculo = require('../models/Veiculo');
const Carro = require('../models/Carro');
const Moto = require('../models/Moto');

describe('POO - Veiculos', () => {
    test('Veiculo funciona como classe abstrata', () => {
        expect(() => new Veiculo({
            marca: 'Teste',
            modelo: 'Base',
            ano: 2024,
            preco: 1000,
            combustivel: 'gasolina'
        })).toThrow('classe abstrata');
    });

    test('Carro herda de Veiculo', () => {
        const carro = new Carro({
            marca: 'BMW',
            modelo: 'M3',
            ano: 2024,
            preco: 89900,
            combustivel: 'gasolina',
            portas: 4
        });

        expect(carro).toBeInstanceOf(Veiculo);
        expect(carro.getNomeCompleto()).toBe('BMW M3');
        expect(carro.getInfoEspecifica()).toBe('4 portas');
    });

    test('Polimorfismo: o mesmo metodo responde de forma diferente', () => {
        const veiculos = [
            new Carro({
                marca: 'Toyota', modelo: 'Corolla', ano: 2023,
                preco: 27500, combustivel: 'hibrido', portas: 5
            }),
            new Moto({
                marca: 'Honda', modelo: 'CBR 600', ano: 2022,
                preco: 11800, combustivel: 'gasolina', cilindradas: 600
            })
        ];

        const infos = veiculos.map((veiculo) => veiculo.getInfoEspecifica());

        expect(infos).toEqual(['5 portas', '600 cc']);
    });
});
