import { Link } from 'react-router-dom';
import { blogPosts } from '../data/site';

export default function Blog() {
  return (
    <>
      <section className="page-hero">
        <div className="container">
          <h1>Blog</h1>
          <p>Receitas, sabores e histórias do Flavors</p>
        </div>
      </section>

      <section className="section">
        <div className="container">
          <div className="blog-grid">
            {blogPosts.map((post) => (
              <article key={post.slug} className="blog-card">
                <div className="blog-card-image">
                  <img src="/images/caril.jpg" alt={post.title} />
                </div>
                <div className="blog-card-content">
                  <h2>
                    <Link to={`/blog/${post.slug}`}>{post.title}</Link>
                  </h2>
                  <p className="blog-meta">
                    {post.author} · {post.date} · {post.readTime}
                  </p>
                  <p>{post.excerpt}</p>
                  <Link to={`/blog/${post.slug}`} className="read-more">
                    Ler mais →
                  </Link>
                </div>
              </article>
            ))}
          </div>
        </div>
      </section>
    </>
  );
}
