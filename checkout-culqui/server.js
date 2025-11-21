import 'dotenv/config';
import express from 'express';
import path from 'path';
import { fileURLToPath } from 'url';
import cors from 'cors';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
app.use(cors());
app.use(express.json());

// servir frontend estático
app.use(express.static(path.join(__dirname, 'public')));

const CULQI_SECRET_KEY = process.env.CULQI_SECRET_KEY;

if (!CULQI_SECRET_KEY || !CULQI_SECRET_KEY.startsWith('sk_test_')) {
  console.warn('❗ Debes configurar CULQI_SECRET_KEY=sk_test_xxx en .env');
}

// Crear cargo en Culqi
app.post('/pay', async (req, res) => {
  try {
    const { tokenId, email, amount } = req.body;

    if (!tokenId || !email) {
      return res.status(400).json({ error: 'Faltan tokenId o email' });
    }

    // amount en céntimos; si no viene, usamos 500 (= S/5.00)
    const amountCents =
      Number.isInteger(amount) && amount > 0 ? amount : 500;

    const body = {
      amount: amountCents,
      currency_code: 'PEN',
      email,
      source_id: tokenId,
    };

    const culqiRes = await fetch('https://api.culqi.com/v2/charges', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${CULQI_SECRET_KEY}`,
      },
      body: JSON.stringify(body),
    });

    const culqiData = await culqiRes.json();

    if (!culqiRes.ok) {
      console.error('Error Culqi:', culqiData);
      return res.status(400).json({
        error:
          culqiData.user_message ||
          culqiData.merchant_message ||
          culqiData.message ||
          'Error procesando el pago en Culqi',
      });
    }

    // OK
    return res.json({ charge: culqiData });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ error: 'Error interno en /pay' });
  }
});

// (Opcional) reembolso
app.post('/refund', async (req, res) => {
  try {
    const { chargeId, amount } = req.body;
    if (!chargeId) {
      return res.status(400).json({ error: 'Falta chargeId' });
    }

    const body = { charge_id: chargeId };
    if (amount) {
      body.amount = amount;
    }

    const culqiRes = await fetch('https://api.culqi.com/v2/refunds', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${CULQI_SECRET_KEY}`,
      },
      body: JSON.stringify(body),
    });

    const culqiData = await culqiRes.json();

    if (!culqiRes.ok) {
      console.error('Error Refund Culqi:', culqiData);
      return res.status(400).json({
        error:
          culqiData.user_message ||
          culqiData.merchant_message ||
          culqiData.message ||
          'Error procesando el reembolso en Culqi',
      });
    }

    return res.json({ refund: culqiData });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ error: 'Error interno en /refund' });
  }
});

// fallback: index.html
app.get('*', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

const PORT = process.env.PORT || 4242;
app.listen(PORT, () => {
  console.log(`🚗 AFinder Culqi server running on http://localhost:${PORT}`);
});