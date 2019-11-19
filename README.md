# odahelp
- odaphp框架自定义命令辅助工具

### 生成service数据库操作类

| 命令名称  | 参数  |  说明 | 备注  |
| ------------ | ------------ | ------------ | ------------ |
| szjkj  | service  | 表名称(szj_admin)  | service依赖models 如果models下没有Admin.php则先生成models下Admin.php，然后再生成service下的Admin.php  |
| szjkj | bean  | 表名称(szj_admin)  | 可单独生成models下Admin.php  |

### 注意事项
- 必须要先配置好数据库配置项
- 该命令只适合于odaphp框架

