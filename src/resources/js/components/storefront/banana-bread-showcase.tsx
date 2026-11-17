import { Compass, Move3d } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';

import { createBananaBreadScene } from '@/lib/banana-bread-scene.js';

type SceneController = ReturnType<typeof createBananaBreadScene>;

/**
 * The 3D banana-bread loaf, drag to turn. Only ever loaded on demand by <TurnTheLoaf> (product page, ≥768px):
 * this module, the scene and the three.js chunk are all fetched on click, never on page load.
 */
export default function BananaBreadShowcase() {
    const stage = useRef<HTMLDivElement>(null);
    const controller = useRef<SceneController | null>(null);
    const [ready, setReady] = useState(false);

    useEffect(() => {
        let cancelled = false;
        import('@/lib/three-showcase.js').then((THREE) => {
            if (cancelled || !stage.current) return;
            controller.current = createBananaBreadScene(THREE, stage.current, { onReady: () => setReady(true) });
        });
        return () => {
            cancelled = true;
            controller.current?.dispose();
            controller.current = null;
        };
    }, []);

    const h = () => controller.current?.handlers;

    return (
        <div className="relative aspect-4/3 w-full overflow-hidden rounded-2xl bg-sidebar select-none">
            <div
                ref={stage}
                className="size-full cursor-grab touch-none active:cursor-grabbing [&_canvas]:outline-none"
                onMouseDown={(e) => h()?.handleMouseDown(e.nativeEvent)}
                onMouseMove={(e) => h()?.handleMouseMove(e.nativeEvent)}
                onMouseUp={() => h()?.handleMouseUp()}
                onMouseLeave={() => h()?.handleMouseLeave()}
                onTouchStart={(e) => h()?.handleTouchStart(e.nativeEvent)}
                onTouchMove={(e) => h()?.handleTouchMove(e.nativeEvent)}
                onTouchEnd={() => h()?.handleTouchEnd()}
                role="img"
                aria-label="3D model of our banana bread loaf. Drag to turn it."
            />
            <span className="pointer-events-none absolute bottom-3 left-3 flex items-center gap-1.5 rounded-full bg-sidebar/90 px-3 py-1 text-xs text-sidebar-primary">
                <Move3d className="size-3.5" /> Drag to turn
            </span>
            <button
                type="button"
                aria-label="Reset view"
                onClick={() => controller.current?.resetCamera()}
                className="absolute top-3 right-3 rounded-full bg-sidebar/90 p-2 text-sidebar-primary hover:bg-sidebar-accent"
            >
                <Compass className="size-4" />
            </button>
            {!ready && (
                <div className="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-sidebar text-sidebar-primary">
                    <div className="size-9 animate-spin rounded-full border-3 border-sidebar-border border-t-sidebar-primary motion-reduce:animate-none" />
                    <p className="text-sm">Warming up the oven…</p>
                </div>
            )}
        </div>
    );
}
