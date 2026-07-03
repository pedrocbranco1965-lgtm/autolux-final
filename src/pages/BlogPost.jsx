import { Link, useParams } from 'react-router-dom';
import { blogPosts } from '../data/site';

export default function BlogPost() {
  const { slug } = useParams();
  const post = blogPosts.find((p) => p.slug === slug);

  if (!post) {
    return (
      <section className="section">
        <div className="container narrow">
          <h1>Artigo não encontrado</h1>
          <Link to="/blog">← Voltar ao Blog</Link>
        </div>
      </section>
    );
  }

  return (
    <>
      <section className="page-hero">
        <div className="container narrow">
          <Link to="/blog" className="back-link">
            ← Voltar ao Blog
          </Link>
          <h1>{post.title}</h1>
          <p className="blog-meta">
            {post.author} · {post.date} · {post.readTime}
          </p>
        </div>
      </section>

      <section className="section">
        <div className="container narrow blog-article">
          <img src="/images/caril.jpg" alt={post.title} className="blog-article-image" />
          <div dangerouslySetInnerHTML={{ __html: post.content }} />
        </div>
      </section>
    </>
  );
}
