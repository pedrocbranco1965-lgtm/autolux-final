import { useEffect, useState } from 'react';

const emptyForm = {
  name: '',
  email: '',
  phone: '',
  vehicleId: '',
  message: '',
  consent: false
};

function ContactForm({ vehicles, selectedVehicleId, requestId }) {
  const [form, setForm] = useState(emptyForm);
  const [errors, setErrors] = useState({});
  const [success, setSuccess] = useState(false);

  useEffect(() => {
    if (selectedVehicleId) {
      setForm((currentForm) => ({ ...currentForm, vehicleId: String(selectedVehicleId) }));
      setSuccess(false);
    }
  }, [selectedVehicleId, requestId]);

  function updateField(event) {
    const { name, value, type, checked } = event.target;
    setForm((currentForm) => ({
      ...currentForm,
      [name]: type === 'checkbox' ? checked : value
    }));
  }

  function validate() {
    const nextErrors = {};
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!form.name.trim()) nextErrors.name = 'Indique o seu nome.';
    if (!form.email.trim()) nextErrors.email = 'Indique o seu email.';
    if (form.email && !emailRegex.test(form.email)) nextErrors.email = 'Introduza um email válido.';
    if (!form.phone.trim()) nextErrors.phone = 'Indique o telefone.';
    if (!form.vehicleId) nextErrors.vehicleId = 'Escolha uma viatura.';
    if (!form.message.trim()) nextErrors.message = 'Escreva uma mensagem.';
    if (!form.consent) nextErrors.consent = 'Confirme que aceita ser contactado.';

    return nextErrors;
  }

  function submitForm(event) {
    event.preventDefault();
    const validationErrors = validate();
    setErrors(validationErrors);

    if (Object.keys(validationErrors).length > 0) {
      setSuccess(false);
      return;
    }

    setSuccess(true);
    setForm(emptyForm);
  }

  return (
    <section className="section contact-section" id="contacto">
      <div className="section-heading">
        <p className="eyebrow">Contacto</p>
        <h2>Peça uma proposta personalizada</h2>
        <p>O formulário valida os campos obrigatórios e permite escolher a viatura pretendida.</p>
      </div>

      {success && (
        <p className="success-message" role="status">
          Pedido registado com sucesso. Esta é uma simulação, por isso nenhum dado foi enviado para um servidor.
        </p>
      )}

      <form className="contact-form" onSubmit={submitForm} noValidate>
        <label>
          Nome *
          <input type="text" name="name" value={form.name} onChange={updateField} />
          {errors.name && <span className="field-error">{errors.name}</span>}
        </label>

        <label>
          Email *
          <input type="email" name="email" value={form.email} onChange={updateField} />
          {errors.email && <span className="field-error">{errors.email}</span>}
        </label>

        <label>
          Telefone *
          <input type="tel" name="phone" value={form.phone} onChange={updateField} />
          {errors.phone && <span className="field-error">{errors.phone}</span>}
        </label>

        <label>
          Viatura *
          <select name="vehicleId" value={form.vehicleId} onChange={updateField}>
            <option value="">Selecione uma viatura</option>
            {vehicles.map((vehicle) => (
              <option key={vehicle.id} value={vehicle.id}>
                {vehicle.marca} {vehicle.modelo} - {vehicle.ano}
              </option>
            ))}
          </select>
          {errors.vehicleId && <span className="field-error">{errors.vehicleId}</span>}
        </label>

        <label className="full-field">
          Mensagem *
          <textarea name="message" rows="5" value={form.message} onChange={updateField} />
          {errors.message && <span className="field-error">{errors.message}</span>}
        </label>

        <label className="checkbox-field full-field">
          <input type="checkbox" name="consent" checked={form.consent} onChange={updateField} />
          Aceito ser contactado pela AutoLux para receber informação sobre esta viatura.
        </label>
        {errors.consent && <span className="field-error full-field">{errors.consent}</span>}

        <button className="button full-field" type="submit">
          Enviar pedido
        </button>
      </form>
    </section>
  );
}

export default ContactForm;
