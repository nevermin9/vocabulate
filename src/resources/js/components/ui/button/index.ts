import { cva, type VariantProps } from 'class-variance-authority'
import { cn } from "@/lib/utils";

export { default as Button } from './Button.vue'

export const buttonVariants = cva(
    cn(
        'relative',
        'inline-flex items-center justify-center flex-[0_0_auto] gap-2 min-w-16',
        'rounded-lg outline-none',
        'whitespace-nowrap text-sm font-medium',
        'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
        'aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive',
        'active:scale-[0.98]',
        '[&_svg]:pointer-events-none [&_svg:not([class*=\'size-\'])]:size-4 [&_svg]:shrink-0',
        'transition-all disabled:pointer-events-none disabled:opacity-50',
        'cursor-pointer disabled:cursor-not-allowed',
        'before:absolute before:inset-0 before:bg-current before:rounded-[inherit] before:pointer-events-none before:opacity-0 before:transition-all hover:before:opacity-[0.08]',
    ),
    {
        variants: {
            variant: {
                default:
                'bg-primary text-primary-foreground shadow-xs ',
                destructive:
                'bg-destructive text-white shadow-xs focus-visible:ring-destructive/20 dark:focus-visible:ring-destructive/40 dark:bg-destructive/60',
                outline:
                'border bg-background shadow-xs dark:bg-input/30 dark:border-input',
                secondary:
                'bg-none text-foreground shadow-xs border-2 border-primary ',
                link: 'text-primary underline-offset-4 hover:underline before:hidden',
            },
            size: {
                default: 'h-10 px-4 py-2 has-[>svg]:px-3',
                sm: 'h-8 gap-1.5 px-3 has-[>svg]:px-2.5',
                lg: 'h-12 px-6 has-[>svg]:px-4',
                icon: 'size-9',
            },
        },
        defaultVariants: {
            variant: 'default',
            size: 'default',
        },
    },
)

export type ButtonVariants = VariantProps<typeof buttonVariants>
