import {
  CircleCheckIcon,
  InfoIcon,
  Loader2Icon,
  OctagonXIcon,
  TriangleAlertIcon,
} from "lucide-react"
import { Toaster as Sonner, type ToasterProps } from "sonner"

const Toaster = ({ ...props }: ToasterProps) => {
  // Colours come only from our tokens (which follow the .dark class), so the toast
  // matches the in-app light/dark choice instead of the OS setting.
  return (
    <Sonner
      className="toaster group"
      icons={{
        success: <CircleCheckIcon className="size-4" />,
        info: <InfoIcon className="size-4" />,
        warning: <TriangleAlertIcon className="size-4" />,
        error: <OctagonXIcon className="size-4" />,
        loading: <Loader2Icon className="size-4 animate-spin" />,
      }}
      style={
        {
          "--normal-bg": "var(--popover)",
          "--normal-text": "var(--popover-foreground)",
          "--normal-border": "var(--border)",
          "--border-radius": "var(--radius)",
          "--success-bg": "color-mix(in oklab, var(--success) 12%, var(--popover))",
          "--success-border": "color-mix(in oklab, var(--success) 35%, var(--popover))",
          "--success-text": "var(--success-ink)",
          "--info-bg": "color-mix(in oklab, var(--info) 10%, var(--popover))",
          "--info-border": "color-mix(in oklab, var(--info) 30%, var(--popover))",
          "--info-text": "var(--info-ink)",
          "--warning-bg": "color-mix(in oklab, var(--warning) 14%, var(--popover))",
          "--warning-border": "color-mix(in oklab, var(--warning) 40%, var(--popover))",
          "--warning-text": "var(--warning-ink)",
          "--error-bg": "color-mix(in oklab, var(--destructive) 10%, var(--popover))",
          "--error-border": "color-mix(in oklab, var(--destructive) 35%, var(--popover))",
          "--error-text": "var(--destructive-ink)",
        } as React.CSSProperties
      }
      {...props}
    />
  )
}

export { Toaster }
