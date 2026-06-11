import { createFileRoute, Link } from "@tanstack/react-router";
import { Check } from "lucide-react";
import { motion } from "framer-motion";

export const Route = createFileRoute("/order-success")({
  head: () => ({ meta: [{ title: "Order Confirmed — NexRun" }] }),
  component: () => (
    <div className="container-nx py-24 text-center max-w-lg mx-auto">
      <motion.div
        initial={{ scale: 0 }}
        animate={{ scale: 1 }}
        transition={{ type: "spring", stiffness: 200 }}
        className="h-20 w-20 rounded-full bg-success grid place-items-center mx-auto"
      >
        <Check className="h-10 w-10 text-success-foreground" />
      </motion.div>
      <h1 className="font-display text-4xl font-black mt-6">Order Confirmed</h1>
      <p className="text-muted-foreground mt-3">
        Thank you for your order. We've sent a confirmation to your email with tracking details.
      </p>
      <p className="font-mono text-sm mt-4">Order #NX-{Math.floor(Math.random() * 90000) + 10000}</p>
      <div className="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
        <Link to="/shop" className="bg-primary text-primary-foreground px-6 py-3 rounded-full font-semibold">Keep shopping</Link>
        <Link to="/account" className="border border-border px-6 py-3 rounded-full font-semibold">View orders</Link>
      </div>
    </div>
  ),
});
