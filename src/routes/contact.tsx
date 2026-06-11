import { createFileRoute } from "@tanstack/react-router";
import { toast } from "sonner";

export const Route = createFileRoute("/contact")({
  head: () => ({ meta: [{ title: "Contact — NexRun" }] }),
  component: Contact,
});

function Contact() {
  return (
    <div className="container-nx py-16 md:py-24 grid md:grid-cols-2 gap-12 max-w-5xl">
      <div>
        <h1 className="font-display text-4xl md:text-5xl font-black">Get in touch</h1>
        <p className="mt-4 text-muted-foreground">
          Questions, feedback, or press inquiries? We typically respond within 24 hours.
        </p>
        <dl className="mt-8 space-y-4 text-sm">
          <div><dt className="eyebrow">Support</dt><dd>support@nexrun.com</dd></div>
          <div><dt className="eyebrow">Press</dt><dd>press@nexrun.com</dd></div>
          <div><dt className="eyebrow">HQ</dt><dd>120 Wythe Ave, Brooklyn NY</dd></div>
        </dl>
      </div>
      <form
        onSubmit={(e) => { e.preventDefault(); toast.success("Message sent"); (e.target as HTMLFormElement).reset(); }}
        className="space-y-4 bg-surface p-8 rounded-xl"
      >
        <label className="block"><span className="text-sm font-medium">Name</span>
          <input required className="mt-1.5 w-full px-4 py-3 rounded-md border border-border bg-background" /></label>
        <label className="block"><span className="text-sm font-medium">Email</span>
          <input required type="email" className="mt-1.5 w-full px-4 py-3 rounded-md border border-border bg-background" /></label>
        <label className="block"><span className="text-sm font-medium">Message</span>
          <textarea required rows={5} className="mt-1.5 w-full px-4 py-3 rounded-md border border-border bg-background" /></label>
        <button className="w-full bg-primary text-primary-foreground py-3.5 rounded-full font-semibold">Send message</button>
      </form>
    </div>
  );
}
