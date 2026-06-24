from sqlalchemy.orm import Query


def page_bounds(page: int = 1, limit: int = 20) -> tuple[int, int]:
    page = max(page, 1)
    limit = min(max(limit, 1), 100)
    return page, limit


def paginate(query: Query, page: int = 1, limit: int = 20):
    page, limit = page_bounds(page, limit)
    total = query.count()
    items = query.offset((page - 1) * limit).limit(limit).all()
    return {
        "items": items,
        "meta": {
            "page": page,
            "limit": limit,
            "total": total,
            "pages": (total + limit - 1) // limit if total else 0,
        },
    }
