defmodule PoolerConfig do
  def config do
    [
      port: 5432,
      database: "postgres",
      pool_size: 20
    ]
  end
end
