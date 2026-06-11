import { createFileRoute } from "@tanstack/react-router";
import storyImg from "@/assets/brand-story.jpg";

export const Route = createFileRoute("/about")({
  head: () => ({
    meta: [
      { title: "About — NexRun" },
      { name: "description", content: "The NexRun story. Built by athletes, for athletes." },
    ],
  }),
  component: About,
});

function About() {
  return (
    <div>
      <section className="container-nx py-16 md:py-24 max-w-3xl">
        <span className="eyebrow text-accent">Our story</span>
        <h1 className="font-display text-5xl md:text-6xl font-black mt-3 leading-tight">
          We don't make gear for everyone. We make it for the obsessed.
        </h1>
        <p className="mt-6 text-lg text-foreground/80">
          NexRun started in a Brooklyn garage in 2019, with one belief: performance products shouldn't compromise.
          Not on materials. Not on fit. Not on the feeling you get when everything just works.
        </p>
      </section>
      <img src={storyImg} alt="Athletes training" className="w-full aspect-[16/7] object-cover" />
      <section className="container-nx py-16 md:py-24 grid md:grid-cols-3 gap-10">
        {[
          ["Performance first", "Every product earns its place through testing — by real athletes, on real terrain."],
          ["Built to outlast", "We use materials and construction methods that hold up. Returns and replacements are easy."],
          ["Lower impact", "Recycled fibers, responsibly sourced rubber, and packaging that breaks down — not the planet."],
        ].map(([t, d]) => (
          <div key={t}>
            <h3 className="font-display text-xl font-bold">{t}</h3>
            <p className="mt-3 text-muted-foreground">{d}</p>
          </div>
        ))}
      </section>
    </div>
  );
}
