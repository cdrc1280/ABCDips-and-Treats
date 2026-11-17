import * as React from "react"
import { cn } from "@/lib/utils"

/** Phone numbers are exactly 11 digits, numbers only (App\Rules\PhilippinePhone). */
export const PHONE_LENGTH = 11

type Bound = number | string | undefined

/** Decimal places a `step` allows: "1"/undefined → 0, "0.01" → 2, "any" → unlimited. */
function decimalsFor(step: Bound): number {
  if (step === "any") return Infinity
  const text = step === undefined ? "1" : String(step)
  const dot = text.indexOf(".")
  return dot === -1 ? 0 : text.length - dot - 1
}

/**
 * Keep only what a number field may hold: digits, one dot (when the step allows decimals,
 * trimmed to the step's places) and a leading minus (only when `min` is absent or negative).
 * Letters, "e", "+", spaces and thousands separators are dropped as they are typed or pasted.
 */
export function sanitizeNumber(raw: string, step?: Bound, min?: Bound): string {
  const decimals = decimalsFor(step)
  const allowNegative = min === undefined || min === "" || Number(min) < 0
  const negative = allowNegative && raw.trimStart().startsWith("-")
  let digits = raw.replace(/[^\d.]/g, "")

  const dot = digits.indexOf(".")
  if (dot !== -1) {
    if (decimals === 0) {
      digits = digits.slice(0, dot)
    } else {
      const fraction = digits.slice(dot + 1).replace(/\./g, "")
      digits = `${digits.slice(0, dot)}.${Number.isFinite(decimals) ? fraction.slice(0, decimals) : fraction}`
    }
  }

  return (negative ? "-" : "") + digits
}

export function sanitizePhone(raw: string): string {
  return raw.replace(/\D/g, "").slice(0, PHONE_LENGTH)
}

/**
 * `type="number"` renders a text box that accepts numbers only (no e/+/scroll-wheel changes);
 * `type="tel"` accepts digits only, max 11. The server re-validates both.
 */
function Input({ className, type, onChange, inputMode, maxLength, step, min, ...props }: React.ComponentProps<"input">) {
  const numeric = type === "number"
  const phone = type === "tel"

  const handleChange =
    numeric || phone
      ? (event: React.ChangeEvent<HTMLInputElement>) => {
          const clean = phone ? sanitizePhone(event.target.value) : sanitizeNumber(event.target.value, step, min)
          if (clean !== event.target.value) event.target.value = clean
          onChange?.(event)
        }
      : onChange

  return (
    <input
      type={numeric ? "text" : type}
      inputMode={phone ? "numeric" : numeric ? (decimalsFor(step) > 0 ? "decimal" : "numeric") : inputMode}
      maxLength={phone ? PHONE_LENGTH : maxLength}
      autoComplete={phone ? (props.autoComplete ?? "tel") : props.autoComplete}
      placeholder={phone ? (props.placeholder ?? "09171234567") : props.placeholder}
      step={numeric ? undefined : step}
      min={numeric ? undefined : min}
      data-numeric={numeric || undefined}
      onChange={handleChange}
      data-slot="input"
      className={cn(
        "h-9 w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none selection:bg-primary selection:text-primary-foreground file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm dark:bg-input/30",
        "focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50",
        "aria-invalid:border-destructive aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40",
        className
      )}
      {...props}
    />
  )
}

export { Input }
