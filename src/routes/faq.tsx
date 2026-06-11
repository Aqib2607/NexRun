import { createFileRoute } from "@tanstack/react-router";
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from "@/components/ui/accordion";

export const Route = createFileRoute("/faq")({
  head: () => ({ meta: [{ title: "FAQ — NexRun" }] }),
  component: FAQ,
});

const ITEMS = [
  ["How long does shipping take?", "Standard ground arrives in 3–5 business days. Free on orders over $100."],
  ["What's your return policy?", "30 days, free returns on unworn items. Refund issued to original payment method."],
  ["Do you ship internationally?", "Yes — we ship to 60+ countries. Duties calculated at checkout."],
  ["How do I find my size?", "Each product page includes a size guide. When in doubt, size up — most styles run true to fit."],
  ["Are products covered by warranty?", "All NexRun footwear comes with a 2-year construction warranty."],
];

function FAQ() {
  return (
    <div className="container-nx py-16 md:py-24 max-w-3xl">
      <h1 className="font-display text-4xl md:text-5xl font-black">Frequently asked</h1>
      <Accordion type="single" collapsible className="mt-10">
        {ITEMS.map(([q, a], i) => (
          <AccordionItem key={i} value={`i-${i}`}>
            <AccordionTrigger className="text-left text-base font-semibold">{q}</AccordionTrigger>
            <AccordionContent className="text-muted-foreground">{a}</AccordionContent>
          </AccordionItem>
        ))}
      </Accordion>
    </div>
  );
}
