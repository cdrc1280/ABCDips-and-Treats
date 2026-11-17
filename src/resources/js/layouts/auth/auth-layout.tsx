import { Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import type { ReactNode } from 'react';

import { Toaster } from '@/components/ui/sonner';
import { useFlashToast } from '@/hooks/use-flash-toast';

const PHOTO = '/images/textures/real_loaf_vertical.jpg';

/**
 * Auth shell: one split card on the cream canvas. Photo panel left with a solid cocoa
 * caption; form right. On phones the photo becomes a short banner above the form.
 */
export default function AuthLayout({ children }: { children: ReactNode }) {
    useFlashToast();
    return (
        <div className="flex min-h-svh flex-col items-center justify-center gap-6 bg-background px-4 py-8 sm:px-6">
            <Link href="/" className="flex items-center gap-2.5">
                <img src="/images/logo.png" alt="" className="size-10 rounded-lg object-contain" />
                <span className="font-display text-lg">ABCDips &amp; Treats</span>
            </Link>

            <div className="grid w-full max-w-[56rem] overflow-hidden rounded-2xl border bg-card md:grid-cols-[minmax(0,5fr)_minmax(0,6fr)]">
                <div className="relative h-36 md:h-auto md:min-h-[34rem]">
                    <img src={PHOTO} alt="A banana bread loaf fresh from the oven" className="absolute inset-0 size-full object-cover" />
                    <div className="absolute inset-x-3 bottom-3 hidden rounded-xl bg-sidebar/92 p-4 text-sidebar-foreground backdrop-blur-sm md:block">
                        <p className="font-display text-lg text-sidebar-primary">Baked daily in Cavite</p>
                        <p className="mt-1 text-sm text-sidebar-foreground/85">
                            Banana bread, brownies, cookies and custom cakes. Small batch, real butter.
                        </p>
                    </div>
                </div>
                <div className="flex flex-col justify-center p-6 sm:p-10">
                    <div className="mx-auto w-full max-w-sm">{children}</div>
                </div>
            </div>

            <Link href="/" className="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft className="size-3.5" /> Back to the menu
            </Link>
            <Toaster position="top-center" richColors />
        </div>
    );
}
