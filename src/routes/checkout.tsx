import { createFileRoute, useNavigate } from "@tanstack/react-router";
import { useCart } from "@/store/cart";
import { money } from "@/lib/format";
import { useState } from "react";
import { Check } from "lucide-react";
import { toast } from "sonner";

export const Route = createFileRoute("/checkout")({
  head: () => ({ meta: [{ title: "Checkout — NexRun" }] }),
  component: Checkout,
});

const STEPS = ["Shipping", "Billing", "Payment", "Review"] as const;

function Checkout() {
  const navigate = useNavigate();
  const { items, subtotal, clear } = useCart();
  const [step, setStep] = useState(0);
  const shipping = subtotal() >= 100 ? 0 : 10;
  const tax = subtotal() * 0.08;
  const total = subtotal() + shipping + tax;

  const next = () => {
    if (step < STEPS.length - 1) setStep(step + 1);
    else {
      clear();
      toast.success("Order placed!");
      navigate({ to: "/order-success" });
    }
  };

  if (items.length === 0) {
    return (
      <div className="container-nx py-24 text-center">
        <h1 className="font-display text-3xl font-bold">Your bag is empty</h1>
      </div>
    );
  }

  return (
    <div className="container-nx py-10 md:py-16">
      <h1 className="font-display text-3xl md:text-4xl font-black mb-8">Checkout</h1>

      {/* Stepper */}
      <ol className="flex items-center gap-2 mb-10 overflow-x-auto">
        {STEPS.map((label, i) => (
          <li key={label} className="flex items-center gap-2 shrink-0">
            <div className={`h-8 w-8 rounded-full grid place-items-center text-xs font-bold transition ${i < step ? "bg-success text-success-foreground" : i === step ? "bg-primary text-primary-foreground" : "bg-muted text-muted-foreground"}`}>
              {i < step ? <Check className="h-4 w-4" /> : i + 1}
            </div>
            <span className={`text-sm font-medium ${i === step ? "" : "text-muted-foreground"}`}>{label}</span>
            {i < STEPS.length - 1 && <div className="w-8 md:w-16 h-px bg-border" />}
          </li>
        ))}
      </ol>

      <div className="grid lg:grid-cols-[1fr_380px] gap-10">
        <div className="bg-surface rounded-xl p-6 md:p-8">
          {step === 0 && <ShippingForm />}
          {step === 1 && <BillingForm />}
          {step === 2 && <PaymentForm />}
          {step === 3 && <Review items={items} />}
          <div className="mt-8 flex gap-3">
            {step > 0 && (
              <button onClick={() => setStep(step - 1)} className="px-6 py-3 border border-border rounded-full font-semibold">
                Back
              </button>
            )}
            <button onClick={next} className="flex-1 bg-primary text-primary-foreground py-3.5 rounded-full font-semibold hover:opacity-90">
              {step === STEPS.length - 1 ? "Place Order" : "Continue"}
            </button>
          </div>
        </div>

        <aside className="bg-background border border-border rounded-xl p-6 h-fit">
          <h2 className="font-display text-lg font-bold mb-4">Order ({items.length})</h2>
          <div className="space-y-3 max-h-72 overflow-y-auto mb-4">
            {items.map((i) => (
              <div key={`${i.productId}-${i.color}-${i.size}`} className="flex gap-3 text-sm">
                <img src={i.image} alt="" className="h-14 w-14 rounded object-cover bg-surface" />
                <div className="flex-1 min-w-0">
                  <p className="truncate font-medium">{i.name}</p>
                  <p className="text-xs text-muted-foreground">{i.color} · {i.size} · ×{i.qty}</p>
                </div>
                <p className="font-medium">{money(i.price * i.qty)}</p>
              </div>
            ))}
          </div>
          <div className="border-t border-border pt-4 space-y-2 text-sm">
            <Row label="Subtotal" value={money(subtotal())} />
            <Row label="Shipping" value={shipping === 0 ? "Free" : money(shipping)} />
            <Row label="Tax" value={money(tax)} />
            <div className="border-t border-border pt-2 mt-2 font-bold text-base flex justify-between">
              <span>Total</span><span>{money(total)}</span>
            </div>
          </div>
        </aside>
      </div>
    </div>
  );
}

function Row({ label, value }: { label: string; value: string }) {
  return <div className="flex justify-between"><span className="text-muted-foreground">{label}</span><span>{value}</span></div>;
}

function Field({ label, type = "text", placeholder }: { label: string; type?: string; placeholder?: string }) {
  return (
    <label className="block">
      <span className="text-sm font-medium">{label}</span>
      <input type={type} placeholder={placeholder} className="mt-1.5 w-full px-4 py-2.5 rounded-md border border-border bg-background" />
    </label>
  );
}

function ShippingForm() {
  return (
    <div>
      <h3 className="font-display text-xl font-bold mb-5">Shipping Address</h3>
      <div className="grid grid-cols-2 gap-4">
        <Field label="First name" />
        <Field label="Last name" />
        <div className="col-span-2"><Field label="Address" /></div>
        <Field label="City" />
        <Field label="Postal code" />
        <Field label="Country" />
        <Field label="Phone" type="tel" />
      </div>
    </div>
  );
}
function BillingForm() {
  return (
    <div>
      <h3 className="font-display text-xl font-bold mb-5">Billing Address</h3>
      <label className="flex items-center gap-2 mb-4 text-sm">
        <input type="checkbox" defaultChecked className="accent-accent" /> Same as shipping address
      </label>
      <div className="grid grid-cols-2 gap-4 opacity-60">
        <Field label="First name" />
        <Field label="Last name" />
        <div className="col-span-2"><Field label="Address" /></div>
      </div>
    </div>
  );
}
function PaymentForm() {
  return (
    <div>
      <h3 className="font-display text-xl font-bold mb-5">Payment</h3>
      <div className="grid gap-4">
        <Field label="Card number" placeholder="1234 5678 9012 3456" />
        <div className="grid grid-cols-2 gap-4">
          <Field label="Expiry" placeholder="MM/YY" />
          <Field label="CVC" placeholder="123" />
        </div>
        <Field label="Name on card" />
      </div>
    </div>
  );
}
function Review({ items }: { items: { name: string; qty: number }[] }) {
  return (
    <div>
      <h3 className="font-display text-xl font-bold mb-5">Review Your Order</h3>
      <p className="text-sm text-muted-foreground mb-4">By placing this order you agree to our terms of service.</p>
      <ul className="text-sm space-y-2">
        {items.map((i, idx) => <li key={idx}>· {i.name} × {i.qty}</li>)}
      </ul>
    </div>
  );
}
