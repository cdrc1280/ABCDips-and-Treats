import { Moon, Sun } from 'lucide-react';

import { Button } from '@/components/ui/button';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { useAppearance } from '@/hooks/use-appearance';

export function AppearanceToggle() {
    const { isDark, updateAppearance } = useAppearance();
    const label = isDark ? 'Switch to light mode' : 'Switch to dark mode';
    return (
        <Tooltip>
            <TooltipTrigger asChild>
                <Button variant="ghost" size="icon" aria-label={label} onClick={() => updateAppearance(isDark ? 'light' : 'dark')}>
                    {isDark ? <Sun /> : <Moon />}
                </Button>
            </TooltipTrigger>
            <TooltipContent>{label}</TooltipContent>
        </Tooltip>
    );
}
