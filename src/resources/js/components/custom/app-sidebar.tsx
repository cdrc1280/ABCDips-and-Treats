import { Link, usePage } from '@inertiajs/react';
import { ChevronRight, ExternalLink, LogOut } from 'lucide-react';

import { AppLogo } from '@/components/custom/app-logo';
import { MenuIcon } from '@/components/custom/menu-icon';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
    SidebarRail,
} from '@/components/ui/sidebar';
import { initials } from '@/lib/format';
import { logout } from '@/routes';
import type { MenuItem } from '@/types';
import { dashboard } from '@/routes/admin';

function isActive(href: string | null, current: string) {
    if (!href) return false;
    const path = current.split('?')[0];
    return href === dashboard().url ? path === dashboard().url : path === href || path.startsWith(`${href}/`);
}

/** Quiet icons; the active row gets the one caramel accent (marker + icon). */
const navButton =
    'relative [&>svg]:text-sidebar-muted-foreground hover:[&>svg]:text-sidebar-accent-foreground data-[active=true]:[&>svg]:text-sidebar-primary before:absolute before:inset-y-2 before:left-0 before:w-[3px] before:rounded-r-full before:bg-sidebar-primary before:opacity-0 data-[active=true]:before:opacity-100 group-data-[collapsible=icon]:before:hidden';

function NavLeaf({ item, current }: { item: MenuItem; current: string }) {
    return (
        <SidebarMenuItem>
            <SidebarMenuButton asChild isActive={isActive(item.href, current)} tooltip={item.title} className={navButton}>
                <Link href={item.href ?? '#'} prefetch>
                    <MenuIcon name={item.icon} />
                    <span>{item.title}</span>
                </Link>
            </SidebarMenuButton>
        </SidebarMenuItem>
    );
}

function NavBranch({ item, current }: { item: MenuItem; current: string }) {
    const open = item.items.some((child) => isActive(child.href, current));
    return (
        <Collapsible asChild defaultOpen={open} className="group/collapsible">
            <SidebarMenuItem>
                <CollapsibleTrigger asChild>
                    <SidebarMenuButton tooltip={item.title} className={navButton}>
                        <MenuIcon name={item.icon} />
                        <span>{item.title}</span>
                        <ChevronRight className="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                    </SidebarMenuButton>
                </CollapsibleTrigger>
                <CollapsibleContent>
                    <SidebarMenuSub>
                        {item.items.map((child) => (
                            <SidebarMenuSubItem key={child.title}>
                                <SidebarMenuSubButton
                                    asChild
                                    isActive={isActive(child.href, current)}
                                    className="text-sidebar-foreground/80 data-[active=true]:bg-transparent data-[active=true]:font-medium data-[active=true]:text-sidebar-primary"
                                >
                                    <Link href={child.href ?? '#'} prefetch>
                                        <span>{child.title}</span>
                                    </Link>
                                </SidebarMenuSubButton>
                            </SidebarMenuSubItem>
                        ))}
                    </SidebarMenuSub>
                </CollapsibleContent>
            </SidebarMenuItem>
        </Collapsible>
    );
}

/** Data-driven sidebar: top-level rows with children render as labelled groups. */
export function AppSidebar() {
    const { menus, auth } = usePage().props;
    const current = usePage().url;
    const user = auth.user;

    const singles = menus.filter((m) => m.items.length === 0);
    const groups = menus.filter((m) => m.items.length > 0);

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild className="hover:bg-transparent active:bg-transparent">
                            <Link href={dashboard().url} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                {singles.length > 0 && (
                    <SidebarGroup>
                        <SidebarMenu>
                            {singles.map((item) => (
                                <NavLeaf key={item.title} item={item} current={current} />
                            ))}
                        </SidebarMenu>
                    </SidebarGroup>
                )}
                {groups.map((group) => (
                    <SidebarGroup key={group.title}>
                        <SidebarGroupLabel className="text-[11px] font-semibold tracking-[0.08em] text-sidebar-muted-foreground uppercase">
                            {group.title}
                        </SidebarGroupLabel>
                        <SidebarMenu>
                            {group.items.map((item) =>
                                item.items.length > 0 ? (
                                    <NavBranch key={item.title} item={item} current={current} />
                                ) : (
                                    <NavLeaf key={item.title} item={item} current={current} />
                                ),
                            )}
                        </SidebarMenu>
                    </SidebarGroup>
                ))}
            </SidebarContent>

            {user && (
                <SidebarFooter>
                    <SidebarMenu>
                        <SidebarMenuItem>
                            <DropdownMenu>
                                <DropdownMenuTrigger asChild>
                                    <SidebarMenuButton size="lg" className="data-[state=open]:bg-sidebar-accent">
                                        <Avatar className="size-8 rounded-md">
                                            <AvatarImage src={user.avatar_url ?? undefined} alt={user.name} />
                                            <AvatarFallback className="rounded-md bg-sidebar-primary text-xs font-semibold text-sidebar-primary-foreground">
                                                {initials(user.name)}
                                            </AvatarFallback>
                                        </Avatar>
                                        <span className="grid flex-1 text-left text-sm leading-tight">
                                            <span className="truncate font-medium">{user.name}</span>
                                            <span className="truncate text-xs text-sidebar-muted-foreground">{user.email}</span>
                                        </span>
                                    </SidebarMenuButton>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent side="top" align="start" className="w-(--radix-dropdown-menu-trigger-width) min-w-56">
                                    <DropdownMenuLabel className="text-xs text-muted-foreground">{auth.roles.join(', ') || 'User'}</DropdownMenuLabel>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem asChild>
                                        <a href="/">
                                            <ExternalLink /> View storefront
                                        </a>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem asChild>
                                        <Link href={logout().url} method="post" as="button" className="w-full">
                                            <LogOut /> Log out
                                        </Link>
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarFooter>
            )}
            <SidebarRail />
        </Sidebar>
    );
}
