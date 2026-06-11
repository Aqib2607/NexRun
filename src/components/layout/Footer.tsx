import { Link } from "@tanstack/react-router";

export function Footer() {
  return (
    <footer className="mt-24 bg-ink text-primary-foreground">
      <div className="container-nx py-16 grid gap-10 md:grid-cols-2 lg:grid-cols-5">
        <div className="lg:col-span-2">
          <div className="font-display text-3xl font-black">NEX<span className="text-accent">RUN</span></div>
          <p className="mt-4 text-sm text-white/60 max-w-sm">
            Performance sportswear engineered for athletes who keep pushing. Crafted with intent, built to outlast.
          </p>
          <form className="mt-6 flex gap-2 max-w-sm" onSubmit={(e) => e.preventDefault()}>
            <input
              type="email"
              placeholder="Email address"
              className="flex-1 bg-white/10 border border-white/15 rounded-md px-4 py-2.5 text-sm placeholder:text-white/40 focus:outline-none focus:border-accent"
            />
            <button className="bg-accent text-accent-foreground px-5 py-2.5 rounded-md text-sm font-semibold hover:opacity-90">
              Join
            </button>
          </form>
        </div>

        {[
          { title: "Shop", links: [["Running", "/category/running"], ["Training", "/category/training"], ["Lifestyle", "/category/lifestyle"], ["All", "/shop"]] },
          { title: "Help", links: [["Contact", "/contact"], ["FAQ", "/faq"], ["Shipping", "/faq"], ["Returns", "/faq"]] },
          { title: "Company", links: [["About", "/about"], ["Careers", "/about"], ["Press", "/about"], ["Sustainability", "/about"]] },
        ].map((c) => (
          <div key={c.title}>
            <h4 className="eyebrow text-white/50 mb-4">{c.title}</h4>
            <ul className="space-y-3">
              {c.links.map(([l, h]) => (
                <li key={l}>
                  <Link to={h} className="text-sm text-white/80 hover:text-accent transition-colors">{l}</Link>
                </li>
              ))}
            </ul>
          </div>
        ))}
      </div>
      <div className="border-t border-white/10">
        <div className="container-nx py-5 flex flex-wrap items-center justify-between gap-3 text-xs text-white/40">
          <span>© {new Date().getFullYear()} NexRun. All rights reserved.</span>
          <span>Privacy · Terms · Cookies</span>
        </div>
      </div>
    </footer>
  );
}
