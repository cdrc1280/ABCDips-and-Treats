import { LegalPage, type LegalProps } from '@/components/storefront/legal-page';

const SECTIONS = [
    {
        title: '1. Information we collect',
        content:
            'We collect what you give us when you create an account, place an order or contact us — your name, email, mobile number and delivery address — plus basic technical data such as your IP address and browser type.',
    },
    {
        title: '2. How we use it',
        content:
            'To bake and deliver your orders, send confirmations and status updates, answer your questions, prevent fraud, and improve our menu and service. We only send promotions if you have agreed to receive them.',
    },
    {
        title: '3. Sharing',
        content:
            'We never sell your personal information. We share only what is needed with the services that help us run the shop: the payment provider (PayMongo) for e-wallet payments and the courier (Lalamove) for deliveries.',
    },
    {
        title: '4. Cookies',
        content:
            'We use essential cookies to keep you signed in, remember your basket and your light/dark preference. You can clear or block cookies in your browser, but the basket and sign-in will not work without them.',
    },
    {
        title: '5. Security',
        content:
            'Our site is served over HTTPS, passwords are stored hashed, and staff access is limited by role. No method is perfectly secure, so please use a strong, unique password.',
    },
    {
        title: '6. Your rights',
        content:
            'Under the Philippine Data Privacy Act you may ask to see, correct or delete your personal information. Update most details in My Profile, or contact us for anything else.',
    },
];

export default function Privacy(props: LegalProps) {
    return <LegalPage title="Privacy Policy" sections={SECTIONS} {...props} />;
}
