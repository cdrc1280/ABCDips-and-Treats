type Handler<E = Event> = (event: E) => void;

export const LIGHTING: Record<'oven' | 'daylight' | 'espresso', { auraLeft: string; auraRight: string }>;

export function createBananaBreadScene(
    three: unknown,
    container: HTMLElement,
    hooks?: { onReady?: () => void; onAutoRotateChange?: (rotating: boolean) => void },
): {
    setLighting(mode: 'oven' | 'daylight' | 'espresso'): void;
    toggleAutoRotate(): void;
    resetCamera(): void;
    handlers: {
        handleMouseDown: Handler<MouseEvent>;
        handleMouseMove: Handler<MouseEvent>;
        handleMouseUp: () => void;
        handleMouseLeave: () => void;
        handleTouchStart: Handler<TouchEvent>;
        handleTouchMove: Handler<TouchEvent>;
        handleTouchEnd: () => void;
    };
    dispose(): void;
};
