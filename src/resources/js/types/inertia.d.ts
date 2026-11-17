import type { AdvisorAnswer, PosReceipt, SharedData, Toast } from '@/types';

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: SharedData;
        flashDataType: { toast?: Toast; receipt?: PosReceipt; advisor?: AdvisorAnswer; cart_drawer?: boolean; cart_undo?: string };
    }
}
