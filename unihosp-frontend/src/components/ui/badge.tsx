import { cva, type VariantProps } from "class-variance-authority";
import { cn } from "@/lib/utils";

const badgeVariants = cva("inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold", {
  variants: {
    variant: {
      default: "bg-muted text-muted-foreground",
      success: "bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200",
      warning: "bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200",
      danger: "bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-200",
      info: "bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200",
    },
  },
  defaultVariants: {
    variant: "default",
  },
});

interface BadgeProps extends React.HTMLAttributes<HTMLSpanElement>, VariantProps<typeof badgeVariants> {}

export function Badge({ className, variant, ...props }: BadgeProps) {
  return <span className={cn(badgeVariants({ variant }), className)} {...props} />;
}
