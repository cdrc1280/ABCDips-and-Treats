import { LegalPage, type LegalProps } from '@/components/storefront/legal-page';

const SECTIONS = [
    {
        title: '1. Acceptance of terms',
        content:
            'By using the ABCDips & Treats website and ordering from us, you agree to these terms. If you do not agree, please do not use the site.',
    },
    {
        title: '2. Orders and payment',
        content:
            'Orders depend on availability and are confirmed by our team. Prices are in Philippine Pesos and are set at checkout. E-wallet orders are prepared once payment is received; bank transfers once we confirm the transfer.',
    },
    {
        title: '3. Delivery',
        content:
            'Delivery times are estimates. Priority delivery fees come from our courier at checkout; group delivery fees are set when your order is batched with nearby orders, before you pay.',
    },
    {
        title: '4. Cancellations and refunds',
        content:
            'You can cancel an order from My Orders while it is still Pending (before we confirm it). Refunds for paid, cancelled orders are processed within 3–5 business days. Custom cakes cannot be refunded once baking has started.',
    },
    {
        title: '5. Product information',
        content:
            'We work to keep descriptions, prices and allergen badges accurate. If you have allergies or dietary needs, check the allergen tab and tell us in your order notes before ordering.',
    },
    {
        title: '6. Intellectual property',
        content: 'Photos, text, logos and recipes on this site belong to ABCDips & Treats and may not be reused without permission.',
    },
    {
        title: '7. Limitation of liability',
        content: 'To the extent allowed by law, our liability for any order is limited to the amount you paid for it.',
    },
];

export default function Terms(props: LegalProps) {
    return <LegalPage title="Terms of Service" sections={SECTIONS} {...props} />;
}
